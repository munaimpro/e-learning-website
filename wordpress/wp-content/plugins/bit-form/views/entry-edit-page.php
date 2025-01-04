<?php
if (!defined('ABSPATH') && !defined('BITFORMS_ASSET_URI')) {
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($title) ? esc_html($title) : ''; ?></title>
  <style>
  html,
  body {
    min-height: 100%;
  }

  body {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    /* background-color: #f1f1f1; */
  }

  ._frm-bg-b<?php echo esc_html($formID) ?> {
    width: 600px;
    margin-block: 100px;
  }
  </style>
  <?php
  $formUpdateVersion = get_option('bit-form_form_update_version');
$bitformCssUrl = BITFORMS_UPLOAD_BASE_URL . '/form-styles/bitform-' . $formID . '.css?bfv=' . $formUpdateVersion;
?>
  <link rel="stylesheet" href="<?php echo esc_url($bitformCssUrl) ?>" />
  <?php
$customCssSubPath = "/form-styles/bitform-custom-{$formID}.css";

$customJsPath = BITFORMS_UPLOAD_BASE_URL . $customCssSubPath . '?ver=' . $formUpdateVersion;
?>
  <?php if(file_exists(BITFORMS_CONTENT_DIR . $customCssSubPath)) : ?>
  <link rel="stylesheet" href="<?php echo esc_url($customJsPath) ?>" />
  <?php endif; ?>

  <?php if (isset($font) && '' !== $font): ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="<?php echo esc_url($font)?>" />
  <?php endif; ?>

</head>

<body>
  <?php echo $formHTML ?>

  <script>
  <?php echo $bfGlobals?>;

  <?php
  $previewJsPath = BITFORMS_UPLOAD_BASE_URL . '/form-scripts/preview-' . $formID . '.js';
?>
  </script>
  <script src="<?php echo esc_url($previewJsPath) ?>"></script>

</body>

</html>