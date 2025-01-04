<?php

namespace BitCode\BitForm\Frontend\Form\View\Conversational\Fields;

class CurrencyField
{
  public static function init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = '')
  {
    $inputWrapper = new ConversationalInputWrapper($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
    $input = self::field($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
    return $inputWrapper->wrapper($input);
  }

  private static function field($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = '')
  {
    $fh = new ConversationalFieldHelpers($formID, $field, $rowID, $form_atomic_Cls_map);
    $img_url = BITFORMS_ASSET_URI . '/../static/currencies/';

    $req = $fh->required();
    $disabled = $fh->disabled();
    $readonly = $fh->readonly();
    $name = $fh->name();
    $ph = $fh->placeholder();
    $selectedFlagImage = '';
    $tabIndx = isset($field->disabled) ? -1 : 0;
    $selectedCurrencyClearable = '';
    $searchPlaceholder = '';
    $searchClearable = '';
    $options = '';
    $readonlyCls = isset($field->readonly) ? 'readonly' : '';
    $disabledCls = isset($field->disabled) ? 'disabled' : '';
    $value = is_string($value) ? $value : '';
    $numValue = preg_replace('/[^0-9.-]/', '', $value);

    $img = htmlentities("data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg'/>");

    if ($fh->property_exists_nested($field, 'config->selectedFlagImage', true)) {
      $selectedFlagImage = <<<FLAGIMAGE
      <div class="{$fh->getConversationalMultiCls('selected-currency-wrp')}">
        <img
          {$fh->getCustomAttributes('selected-currency-img')}
          class="{$fh->getConversationalMultiCls('selected-currency-img')} {$fh->getCustomClasses('selected-currency-img')}"
          aria-hidden="true"
          alt="selected country flag"
          src="{$img}"
        />
      </div>
FLAGIMAGE;
    }

    if ($fh->property_exists_nested($field, 'config->selectedCurrencyClearable', true)) {
      $selectedCurrencyClearable = <<<CLEARABLE
      <button
        {$fh->getCustomAttributes('input-clear-btn')}
        type="button"
        title="Clear selected currency value"
        class="{$fh->getConversationalMultiCls('input-clear-btn')} {$fh->getCustomClasses('input-clear-btn')}"
      >
        <svg
          width="12"
          height="12"
          role="img"
          title="Cross icon"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <line x1="18" y1="6" x2="6" y2="18" />
          <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
      </button>
CLEARABLE;
    }

    if ($fh->property_exists_nested($field, 'config->searchPlaceholder', '', 1)) {
      $searchPlaceholder = "placeholder='{$fh->esc_attr($field->config->searchPlaceholder)}'";
    }

    if ($fh->property_exists_nested($field, 'config->searchClearable', true)) {
      $searchClearable = <<<CLEARABLE
      <button
        {$fh->getCustomAttributes('search-clear-btn')}
        type="button"
        title="Clear search"
        class="{$fh->getConversationalMultiCls('icn')} {$fh->getConversationalMultiCls('search-clear-btn')} {$fh->getCustomClasses('search-clear-btn')}"
        tabIndex="-1"
      >
        <svg
          width="12"
          height="12"
          role="img"
          title="Cross icon"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <line x1="18" y1="6" x2="6" y2="18" />
          <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
      </button>
CLEARABLE;
    }

    return <<<CURRENCYFIELD
    <div class="{$fh->getConversationalMultiCls('currency-fld-container')}">
    <div
      {$fh->getCustomAttributes('currency-fld-wrp')}
      class="{$fh->getConversationalMultiCls('currency-fld-wrp')} {$fh->getCustomClasses('currency-fld-wrp')} {$disabled} {$readonly}"
    >
      <input
        {$fh->getCustomAttributes('currency-hidden-input')}
        {$name}
        {$req}
        type="text"
        title="Currency Hidden Input"
        class="{$fh->getConversationalMultiCls('currency-hidden-input')} d-none"
        {$disabled}
        {$readonly}
        value="{$fh->esc_attr($value)}"
      />
      <div class="{$fh->getConversationalMultiCls('currency-inner-wrp')}">
        <div
          {$fh->getCustomAttributes('dpd-wrp')}
          class="{$fh->getConversationalMultiCls('dpd-wrp')} {$fh->getCustomClasses('dpd-wrp')}"
          role="combobox"
          aria-controls="currency-dropdown"
          aria-live="assertive"
          aria-labelledby="currency-label-2"
          aria-expanded="false"
          tabIndex="{$tabIndx}"
        >
          {$selectedFlagImage}
          <div class="{$fh->getConversationalMultiCls('dpd-down-btn')}">
            <svg
              width="15"
              height="15"
              role="img"
              title="Downarrow icon"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <polyline points="6 9 12 15 18 9" />
            </svg>
          </div>
        </div>
        <input
          {$fh->getCustomAttributes('currency-amount-input')}
          aria-label="Currency Input"
          type="text"
          class="{$fh->getConversationalMultiCls('currency-amount-input')} {$fh->getCustomClasses('currency-amount-input')}"
          {$ph}
          tabIndex="{$tabIndx}"
          data-num-value="{$numValue}"
        />
       {$selectedCurrencyClearable}
      </div>
      <div 
        {$fh->getCustomAttributes('option-wrp')}
        class="{$fh->getConversationalMultiCls('option-wrp')} {$fh->getCustomClasses('option-wrp')}"
      >
        <div class="{$fh->getConversationalMultiCls('option-inner-wrp')}">
          <div
            {$fh->getCustomAttributes('option-search-wrp')}
            class="{$fh->getConversationalMultiCls('option-search-wrp')} {$fh->getCustomClasses('option-search-wrp')}"
          >
            <input
              {$fh->getCustomAttributes('opt-search-input')}
              type="search"
              class="{$fh->getConversationalMultiCls('opt-search-input')} {$fh->getCustomClasses('opt-search-input')}"
              {$searchPlaceholder}
              autoComplete="off"
              tabIndex="-1"
            />
            <svg
              {$fh->getCustomAttributes('opt-search-icn')}
              class="{$fh->getConversationalMultiCls('icn')} {$fh->getConversationalMultiCls('opt-search-icn')} {$fh->getCustomClasses('opt-search-icn')}"
              aria-hidden="true"
              width="22"
              height="22"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <circle cx="11" cy="11" r="8" />
              <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
            {$searchClearable}
          </div>
          <ul
            {$fh->getCustomAttributes('option-list')}
            class="{$fh->getConversationalMultiCls('option-list')} {$fh->getCustomClasses('option-list')}"
            tabIndex="-1"
            role="listbox"
            aria-label="currency list"
          >
          {$options}
          </ul>
        </div>
      </div>
    </div>
  </div>
CURRENCYFIELD;
  }
}
