# CaptchaBreaker

Lib for captcha break

Supported providers:
* [AntiCaptcha](https://anti-captcha.com/)
* [CapMonster](https://capmonster.cloud/)

## Install
`composer require crawly/captcha-breaker`

## Usage

### Image Captcha

```
$clientKey = '0cc175b9c0f1b6a831c399e269772661';
$base64Image = 'MTExMTExMTEx';

$imageToText = new ImageToText($clientKey, $base64Image);
$text = $imageToText->solve(); // deditur
```

### Math Captcha

```
$clientKey = '0cc175b9c0f1b6a831c399e269772661';
$base64Image = 'MTExMTExMTEx';

$imageToText = new ImageToText($clientKey, $base64Image, null, false, false, 1, true);
$text = $imageToText->solve(); // 56
```

### NoCaptcha

```
$clientKey = '0cc175b9c0f1b6a831c399e269772661';
$websiteURL = 'http://test.org';
$websiteKey = '6Lc_aCMTAAAAABx7u2N0D1XnVbI_v6ZdbM6rYf16';

$imageToText = new NoCaptcha($clientKey, $websiteURL, $websiteKey);
$text = $imageToText->solve(); // 3AHJ_VuvYIBNBW5yyv0zRYJ75VkOKvhKj9_xGBJKnQimF72rfoq3Iy-DyGHMwLAo6a3
```

### ReCaptchaV3

```
$clientKey = '0cc175b9c0f1b6a831c399e269772661';
$websiteURL = 'http://test.org';
$websiteKey = '6Lc_aCMTAAAAABx7u2N0D1XnVbI_v6ZdbM6rYf16';

$imageToText = new ReCaptchaV3($clientKey, $websiteURL, $websiteKey, 'myverify', ReCaptchaV3::MIN_SCORE_0_7);
$text = $imageToText->solve(); // 3AHJ_VuvYIBNBW5yyv0zRYJ75VkOKvhKj9_xGBJKnQimF72rfoq3Iy-DyGHMwLAo6a3
```

## Log
[PSR Log](https://github.com/php-fig/log) support

```
$clientKey = '0cc175b9c0f1b6a831c399e269772661';
$base64Image = 'MTExMTExMTEx';

$imageToText = new ImageToText($clientKey, $base64Image, $logger);
$text = $imageToText->solve(); // deditur
```