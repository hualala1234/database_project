<?php
ob_start();
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
include('../dbh.php');


use setasign\Fpdi\Tcpdf\Fpdi;
use setasign\Fpdi\PdfReader\PdfReader;

$tranId = intval($_GET['tranId'] ?? 0);
$cid = $_SESSION["cid"] ?? '';
if ($cid !== '') {
    $sql = "SELECT * FROM Customer WHERE cid = $cid";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
}

// 取得交易與外送資料
$sql = "
SELECT t.*, d.dpName, d.latitude AS dLatitude, d.longitude AS dLongitude, o.orderStatus AS deliveryStatus, o.arrivePicture
FROM Transaction t
LEFT JOIN deliveryperson d ON t.did = d.did
LEFT JOIN dOrders o ON o.tranId = t.tranId
WHERE t.tranId = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tranId);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
if (!$order) die("找不到訂單");

// 商品明細
$itemSql = "
SELECT r.*, p.pName
FROM Record r
JOIN Product p ON r.pid = p.pid
WHERE r.tranId = ?
";
$itemStmt = $conn->prepare($itemSql);
$itemStmt->bind_param("i", $tranId);
$itemStmt->execute();
$items = $itemStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// 計算金額
$subtotal = array_sum(array_column($items, 'salePrice'));
$deliveryFee = 30;
$serviceFee = $subtotal * 0.05;
$discountRate = 1.0;
$couponText = '無';
switch ($order['couponCode']) {
    case 'CLAWWIN15': $discountRate = 0.85; $couponText = 'CLAWWIN15（15%折扣）'; break;
    case 'CLAWWIN20': $discountRate = 0.80; $couponText = 'CLAWWIN20（20%折扣）'; break;
    case 'CLAWWIN30': $discountRate = 0.70; $couponText = 'CLAWWIN30（30%折扣）'; break;
    case 'CLAWSHIP':  $deliveryFee = 0;      $couponText = 'CLAWSHIP（免運費）'; break;
}
$total = $subtotal * $discountRate + $deliveryFee + $serviceFee;

// 建立 PDF（用 TCPDF）
// 先定義字型目錄（如果沒有設定的話）
if (!defined('K_PATH_FONTS')) {
    define('K_PATH_FONTS', __DIR__ . '/../tcpdf/fonts/');
}

$pdf = new Fpdi();
$pdf->AddPage();
$pdf->setFontSubsetting(true);
$pdf->SetFont('notosanstcvariablefont_wght', '', 12);

$html = <<<EOD
<h1>訂單明細 #{$tranId}</h1>
<p><strong>地址：</strong> {$order['address_text']}</p>
<p><strong>外送員：</strong> {$order['dpName']}</p>
<table border="1" cellpadding="4">
<thead><tr><th>商品名稱</th><th>數量</th><th>單價</th><th>總價</th></tr></thead>
<tbody>
EOD;

foreach ($items as $item) {
    $html .= "<tr>
        <td>" . htmlspecialchars($item['pName']) . "</td>
        <td>" . intval($item['quantity']) . "</td>
        <td>$" . number_format($item['price'], 0) . "</td>
        <td>$" . number_format($item['salePrice'], 0) . "</td>
    </tr>";
}

$subtotalFormatted = number_format($subtotal * $discountRate, 0);
$deliveryFeeFormatted = number_format($deliveryFee, 0);
$serviceFeeFormatted = number_format($serviceFee, 0);
$totalFormatted = number_format($total, 0);

$html .= <<<EOD
<p><strong>優惠券：</strong> {$couponText}</p>
<p><strong>小計：</strong> \${$subtotalFormatted}</p>
<p><strong>運費：</strong> \${$deliveryFeeFormatted}</p>
<p><strong>服務費：</strong> \${$serviceFeeFormatted}</p>
<p><strong>總金額：</strong> \${$totalFormatted}</p>
<p><strong>付款方式：</strong> {$order['paymentMethod']}</p>
EOD;

$pdf->writeHTML($html, true, false, true, false, '');

// PDF 暫存路徑
$pdfPath = __DIR__ . "/signed_order_{$tranId}.pdf";
$pdf->Output($pdfPath, 'F');

// 載入憑證與私鑰（openssl 自簽方式）
$certPath = realpath(__DIR__ . '/../certificate.crt');
$keyPath = realpath(__DIR__ . '/../private_key.pem');
$certContent = file_get_contents($certPath);
$keyContent = file_get_contents($keyPath);


if (!$certContent || !$keyContent) {
    die("憑證或私鑰讀取失敗！");
}

// 計算 PDF 的雜湊值
$rawPdf = file_get_contents($pdfPath);
$hash = hash('sha256', $rawPdf, true);

// 使用私鑰簽名
openssl_sign($hash, $signature, $keyContent, OPENSSL_ALGO_SHA256);

// 簽章附加在 PDF 結尾（模擬，真實用 CMS 簽名封裝會較完整）
file_put_contents($pdfPath, $rawPdf . "\n%Signature:\n" . base64_encode($signature), LOCK_EX);
file_put_contents("signed_order_{$tranId}.sig", base64_encode($signature));

ob_end_clean();
// 輸出 PDF
header('Content-Type: application/pdf');
header("Content-Disposition: inline; filename=order_{$tranId}_signed.pdf");
readfile($pdfPath);

// 移除暫存




?>
