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
  * {
    padding: 0;
    margin: 0;
    box-sizing: border-box;
  }

  .standalone-form-container {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .standalone-form-wrapper {
    width: 40%;
    /* Additional styling for the container, if needed */
  }

  @media (max-width: 575.98px) {
    .standalone-form-wrapper {
      width: 100%;
    }
  }

  ._frm-bg-b<?php echo esc_html($formID);

?> {
    width: 100%;
  }
  </style>
  <?php
$baseCSSPath = "/form-styles/bitform-{$formID}.css";
$customCSSPath = "/form-styles/bitform-custom-{$formID}.css";
$standaloneCSSPath = "/form-styles/bitform-standalone-{$formID}.css";
?>
  <link rel="stylesheet" href="<?php echo esc_url(BITFORMS_UPLOAD_BASE_URL . $baseCSSPath)?>" />

  <?php if (file_exists(BITFORMS_CONTENT_DIR . $customCSSPath)) : ?>
  <link rel="stylesheet" href="<?php echo esc_url(BITFORMS_UPLOAD_BASE_URL . $customCSSPath) ?>" />
  <?php endif; ?>

  <?php if (file_exists(BITFORMS_CONTENT_DIR . $standaloneCSSPath)) : ?>
  <link rel="stylesheet" href="<?php echo esc_url(BITFORMS_UPLOAD_BASE_URL . $standaloneCSSPath) ?>" />
  <?php endif; ?>

  <?php if (isset($font) && '' !== $font) : ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="<?php echo esc_url($font) ?>" />
  <?php endif; ?>

</head>

<body>
  <div class="standalone-form-container">
    <div class="standalone-form-wrapper">
      <?php echo $formHTML ?>
    </div>
  </div>

  <script>
  <?php echo $bfGlobals ?>;
  <?php
  $previewJsPath = BITFORMS_UPLOAD_BASE_URL . '/form-scripts/preview-' . $formID . '.js';
?>
  </script>
  <script src="<?php echo esc_url($previewJsPath) ?>"></script>
  </div>
</body>

</html>