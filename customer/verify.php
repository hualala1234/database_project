<?php
// 簽章驗證檔案 verify.php
$tranId = intval($_GET['tranId'] ?? 0);
$pdfPath = __DIR__ . "/signed_order_{$tranId}.pdf";
$certPath = realpath(__DIR__ . '/../certificate.crt');

if (!file_exists($pdfPath)) {
    die("找不到已簽章的 PDF");
}
if (!file_exists($certPath)) {
    die("找不到憑證檔案");
}

// 載入 PDF
$signedPdf = file_get_contents($pdfPath);

// 拆出原始 PDF 和簽章
$signatureMarker = "%Signature:";
$signaturePos = strpos($signedPdf, $signatureMarker);
if ($signaturePos === false) {
    die("找不到簽章資料");
}

$rawPdf = substr($signedPdf, 0, $signaturePos);
$signatureBase64 = trim(substr($signedPdf, $signaturePos + strlen($signatureMarker)));
$signature = base64_decode(trim($signatureBase64));

// 驗證簽章
// 載入憑證內容（不是檔案路徑）
$certContent = file_get_contents($certPath);
if (!$certContent) die("無法讀取憑證內容");

// 取得公鑰資源
$pubKey = openssl_pkey_get_public($certContent);
if (!$pubKey) die("無法載入公鑰");

// 驗證簽章
$ok = openssl_verify(hash('sha256', $rawPdf, true), $signature, $pubKey, OPENSSL_ALGO_SHA256);

echo $ok === 1 ? "簽章有效" : ($ok === 0 ? "簽章無效" : "驗證錯誤：" . openssl_error_string());
?>
