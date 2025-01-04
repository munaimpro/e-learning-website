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
    font-family: sans-serif;
  }

  ._frm-bg-b<?php echo esc_html($formID);

?> {
    width: min(961px, 95%);
    margin-block: 100px;
  }
  </style>
  <?php
 $formUpdateVersion = get_option('bit-form_form_update_version');
$cssUrl = BITFORMS_UPLOAD_BASE_URL . '/form-styles/bitform-' . $formID . '.css?bfv=' . $formUpdateVersion;
?>
  <link rel="stylesheet" href="<?php echo esc_url($cssUrl) ?>" />
  <?php
$customCssSubPath = '/form-styles/bitform-custom-' . $formID . '.css';
?>
  <?php if(file_exists(BITFORMS_CONTENT_DIR . $customCssSubPath)) : ?>
  <link rel="stylesheet" href="<?php echo esc_url(BITFORMS_UPLOAD_BASE_URL . $customCssSubPath) ?>" />
  <?php endif; ?>

  <?php if (isset($font) && '' !== $font): ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="<?php echo esc_url($font) ?>" />
  <?php endif; ?>

</head>

<body>
  <?php echo $formHTML?>

  <script>
  <?php
echo $bfGlobals;
$previewJsUrl = BITFORMS_UPLOAD_BASE_URL . '/form-scripts/preview-' . $formID . '.js?bfv=' . $formUpdateVersion;
?>;
  </script>
  <script src="<?php echo esc_url($previewJsUrl) ?>">
  </script>


  <div style="position:fixed;top:0;left:0;border:1px solid lightgray;background:#fafafa;padding:10px">
    <?php
function readable_filesize($bytes, $decimals = 2)
{
  $sz = 'BKMGTP';
  $factor = floor((strlen($bytes) - 1) / 3);
  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$sz[$factor];
}
?>
    <div>Form ID : <?php echo isset($formID) ? esc_html($formID) : ''; ?></div>
    <div>JS size =
      <?php echo readable_filesize(filesize(BITFORMS_CONTENT_DIR . '/form-scripts/preview-' . $formID . '.js')); ?>
    </div>
    <div>CSS size =
      <?php echo readable_filesize(filesize(BITFORMS_CONTENT_DIR . '/form-styles/bitform-' . $formID . '.css')); ?>
    </div>
  </div>
</body>

</html>