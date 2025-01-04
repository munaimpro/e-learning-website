<?php

namespace BitCode\BitForm\Frontend\Form\View\Theme\Fields;

class PhoneNumberField
{
  public static function init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    $inputWrapper = new ClassicInputWrapper($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
    $input = self::field($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
    return $inputWrapper->wrapper($input);
  }

  private static function field($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    $fh = new ClassicFieldHelpers($field, $rowID, $form_atomic_Cls_map);
    $img_url = BITFORMS_ASSET_URI . '/../static/countries/';
    $req = $fh->required();
    $disabled = $fh->disabled();
    $readonly = $fh->readonly();
    $name = $fh->name();
    $ph = $fh->placeholder();
    $selectedFlagImage = '';
    $tabIndx = isset($field->disabled) ? -1 : 0;
    $selectedCountryClearable = '';
    $searchPlaceholder = '';
    $searchClearable = '';
    $options = '';
    $readonlyCls = isset($field->readonly) ? 'readonly' : '';
    $disabledCls = isset($field->disabled) ? 'disabled' : '';
    $img = htmlentities("data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg'></svg>");

    $val = $fh->value();

    if ($fh->property_exists_nested($field, 'config->selectedFlagImage', true)) {
      $selectedFlagImage = <<<FLAGIMAGE
      <div class="{$fh->getAtomicCls('selected-country-wrp')}">
        <img
          {$fh->getCustomAttributes('selected-phone-img')}
          alt="Selected Country image"
          aria-hidden="true"
          class="{$fh->getAtomicCls('selected-country-img')} {$fh->getCustomClasses('selected-country-img')}"
          src="{$img}"
        />
    </div>
FLAGIMAGE;
    }

    if ($fh->property_exists_nested($field, 'config->selectedCountryClearable', true)) {
      $selectedCountryClearable = <<<CLEARABLE
      <button
        {$fh->getCustomAttributes('input-clear-btn')}
        type="button"
        title="Clear value"
        class="{$fh->getAtomicCls('input-clear-btn')} {$fh->getCustomClasses('input-clear-btn')}"
      >
        <svg
          width="12"
          height="12"
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
      $searchPlaceholder = "{$field->config->searchPlaceholder}";
    }

    if ($fh->property_exists_nested($field, 'config->searchClearable', true)) {
      $searchClearable = <<<CLEARABLE
      <button
        {$fh->getCustomAttributes('search-clear-btn')}
        type="button"
        aria-label="Clear search"
        class="{$fh->getAtomicCls('search-clear-btn')} {$fh->getCustomClasses('search-clear-btn')}"
        tabIndex="-1"
      >
        <svg
          width="12"
          height="12"
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

    return <<<PHONENUMBERFIELD
    <div class="{$fh->getAtomicCls('phone-fld-container')}">
      <div 
        {$fh->getCustomAttributes('phone-fld-wrp')}
        class="{$fh->getAtomicCls('phone-fld-wrp')} {$fh->getCustomClasses('phone-fld-wrp')} {$readonly} {$disabled}"
      >
        <input
          {$name}
          {$req}
          type="text"
          title="Phone-number Hidden Input"
          class="{$fh->getAtomicCls('phone-hidden-input')} d-none"
          {$fh->disabled()}
          {$fh->readonly()}
          {$val}
        />
        <div class="{$fh->getAtomicCls('phone-inner-wrp')}">
          <div
            class="{$fh->getAtomicCls('dpd-wrp')}"
            role="combobox"
            aria-live="assertive"
            aria-labelledby="country-label-2"
            aria-expanded="false"
            tabIndex={$tabIndx}
          >
            {$selectedFlagImage}
            <div class="{$fh->getAtomicCls('dpd-down-btn')}">
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
            {$fh->getCustomAttributes('phone-number-input')}
            aria-label="Phone Number"
            type="tel"
            class="{$fh->getAtomicCls('phone-number-input')} {$fh->getCustomClasses('phone-number-input')}"
            autoComplete="tel"
            {$ph}
            tabIndex={$tabIndx}
          />
          {$selectedCountryClearable}
        </div>
        <div 
          {$fh->getCustomAttributes('option-wrp')}
          class="{$fh->getAtomicCls('option-wrp')} {$fh->getCustomClasses('option-wrp')}"
        >
          <div class="{$fh->getAtomicCls('option-inner-wrp')}">
            <div 
              {$fh->getCustomAttributes('option-search-wrp')}
              class="{$fh->getAtomicCls('option-search-wrp')} {$fh->getCustomClasses('option-search-wrp')}"
            >
              <input
                {$fh->getCustomAttributes('opt-search-input')}
                aria-label="Search for countries"
                type="search"
                class="{$fh->getAtomicCls('opt-search-input')} {$fh->getCustomClasses('opt-search-icn')}"
                placeholder="{$fh->esc_attr($searchPlaceholder)}"
                autoComplete="off"
                tabIndex="-1"
              />
              <svg
                {$fh->getCustomAttributes('opt-search-icn')}
                class="{$fh->getAtomicCls('opt-search-icn')} {$fh->getCustomClasses('opt-search-icn')}"
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
              class="{$fh->getAtomicCls('option-list')} {$fh->getCustomClasses('option-list')}"
              tabIndex="-1"
              role="listbox"
              aria-label="country list"
            >
              {$options}
            </ul>
          </div>
        </div>
      </div>
    </div>
PHONENUMBERFIELD;
  }
}
