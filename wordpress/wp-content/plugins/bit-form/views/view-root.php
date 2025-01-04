<?php if (!defined('ABSPATH') && !defined('BITFORMS_ASSET_URI')) {
  exit;
}

$logoUrl = BITFORMS_ASSET_URI . '/logo.svg';

?>

<noscript>You need to enable JavaScript to run this app.</noscript>
<style>
.__root-wrp {
  display: grid;
  place-content: center;
  height: 90vh;
  text-align: center;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.__root-logo {
  margin-inline: auto;
}

.__root-title {
  color: white;
}

.__root-subtitle {
  color: gray;
}
</style>

<div id="btcd-app">
  <div class="__root-wrp">
    <img alt="bitform-logo" class="__root-logo" width="40" src="<?php echo esc_url($logoUrl) ?>">
    <h1 class="__root-title">Welcome to Bit Form.</h1>
    <p class="__root-subtitle">Modern form solution in Wordpress</p>
  </div>
</div>
<script>
const {
  backgroundColor
} = window.getComputedStyle(document.querySelector('#wpadminbar'))
document.querySelector('#wpbody').style.backgroundColor = backgroundColor
document.querySelector('#wpcontent').style.paddingLeft = 0
</script>
<script defer type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.8/xlsx.full.min.js"></script>