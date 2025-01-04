<?php

namespace BitCode\BitForm\Frontend\Form\View\Conversational\Fields;

class DropdownField
{
  public static function init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    $inputWrapper = new ConversationalInputWrapper($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
    $input = self::field($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
    return $inputWrapper->wrapper($input);
  }

  private static function field($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    $fh = new ConversationalFieldHelpers($formID, $field, $rowID, $form_atomic_Cls_map);
    $ph = isset($field->ph) ? $field->ph : '';
    $val = !empty($value) ? $value : $fh->value();
    $val = 'array' === gettype($val) ? ("value='" . implode(',', $val) . "'") : $val;
    $name = $fh->name();
    $req = $fh->required();
    $readonlyCls = isset($field->valid->readonly) ? 'readonly' : '';
    $disabledCls = isset($field->valid->disabled) ? 'disabled' : '';
    $selectedOptImage = '';
    $optionsList = '';
    $activeList = isset($field->config->activeList) ? $field->config->activeList : null;
    $optionIcon = '';
    $allowCustomOption = isset($field->config->allowCustomOption) ? $field->config->allowCustomOption : false;
    $img = htmlentities("data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg'/>");

    if ($fh->property_exists_nested($field, 'config->optionIcon', true)) {
      $optionIcon = <<<OPTIONICON
      <img
        {$fh->getCustomAttributes('opt-icn')}
        class="opt-icn {$fh->getCustomClasses('opt-icn')}"
        src="{$img}"
        alt="BD"
        loading="lazy"
      />

OPTIONICON;
    }

    if ($fh->property_exists_nested($field, 'config->selectedOptImage', true)) {
      $selectedOptImage = <<<SELECTEDoPTIMAGE
      <img
        {$fh->getCustomAttributes('selected-opt-img')}
        class="{$fh->getConversationalMultiCls('selected-opt-img')} placeholder-img {$fh->getCustomClasses('selected-opt-img')}"
        aria-hidden="true"
        alt="selected option icon"
        src="{$img}"
      >
SELECTEDoPTIMAGE;
    }

    if (property_exists($field, 'optionsList')) {
      foreach ($field->optionsList as $key => $value) {
        $dataIndex = 0;
        $valueArr = (array) $value;
        $listName = array_keys($valueArr)[0];
        $options = array_values($valueArr)[0];

        $optionsList .= <<<OPTIONSLIST
            <ul
              {$fh->getCustomAttributes('option-list')}
              class="{$fh->getClassWithFieldKey('option-list')} {$fh->getConversationalCls('dpd-option-list')} {$fh->getCustomClasses('option-list')}"
              aria-hidden="true"
              aria-label="Option List"
              data-list="{$fh->esc_attr($listName)}"
              data-list-index="{$key}"
              tabIndex="-1"
              role="listbox"
            >
OPTIONSLIST;
        if ($allowCustomOption) {
          $optionsList .= <<<CREATEOPT
          <li
          {$fh->getCustomAttributes('option')}
          data-index={$dataIndex}
          data-value="create-opt"
          class="option create-opt {$fh->getCustomClasses('option')}"
          role="option"
          aria-selected="false"
          tabIndex="-1"
          style="display: none !important;"
          >
          <span 
            {$fh->getCustomAttributes('opt-lbl-wrp')}
            class="opt-lbl-wrp {$fh->getCustomClasses('opt-lbl-wrp')}" 
          >
            <span 
              {$fh->getCustomAttributes('opt-lbl')}
              class="opt-lbl {$fh->getCustomClasses('opt-lbl')}" 
            > 
              Create: 
            </span>
          </span>
          <span class="opt-prefix"></span>
        </li>
CREATEOPT;
        }

        foreach ($options as $opt) {
          if (isset($opt->type)) {
            $disableOpt = isset($opt->disabled) ? 'disabled-opt' : '';
            $optionsList .= <<<OPTIOINS
            <li data-index={$dataIndex}
              {$fh->getCustomAttributes('option')}
              class="option opt-group-title {$fh->getCustomClasses('option')} {$disableOpt}"
            >
              <span 
                {$fh->getCustomAttributes('opt-lbl')}
                class="opt-lbl {$fh->getCustomClasses('opt-lbl')}"
              >
                {$fh->kses_post($opt->title)}
              </span>
            </li>
            
OPTIOINS;
            foreach ($opt->childs as $child) {
              $optVal = isset($child->val) ? $child->val : $child->lbl;
              $disableOpt = (isset($opt->disabled) || isset($child->disabled)) ? 'disabled-opt' : '';
              $optImage = '';
              if (isset($child->img)) {
                $optImage = <<<OPTIMAGE
                <img
                  {$fh->getCustomAttributes('opt-icn')}
                  class="opt-icn {$fh->getCustomClasses('opt-icn')}"
                  aria-hidden="true"
                  alt="{$fh->esc_attr($child->lbl)}"
                  src="{$fh->esc_url($child->img)}"
                >
OPTIMAGE;
              }
              $optionsList .= <<<OPTIONS_CHILDREN
              <li
                {$fh->getCustomAttributes('option')}
                data-index="{$dataIndex}"
                data-value="{$fh->esc_attr($optVal)}"
                class="option opt-group-child {$fh->getCustomClasses('option')} {$disableOpt}"
                role="option"
                aria-selected="false"
                tabIndex="-1"
              >
                <span 
                  {$fh->getCustomAttributes('opt-lbl-wrp')}
                  class="opt-lbl-wrp {$fh->getCustomClasses('opt-lbl-wrp')}" 
                >
                  {$optImage}
                  <span 
                    {$fh->getCustomAttributes('opt-lbl')}
                    class="opt-lbl {$fh->getCustomClasses('opt-lbl')}" 
                  >
                    {$fh->kses_post($child->lbl)}
                  </span>
                </span>
                <span class="opt-prefix" />
              </li>
OPTIONS_CHILDREN;
            }
          } else {
            $optVal = isset($opt->val) ? $opt->val : $opt->lbl;
            $disableOpt = isset($opt->disabled) ? 'disabled-opt' : '';
            $optImage = '';
            if (isset($opt->img)) {
              $optImage = <<<OPTIMAGE
                <img
                  {$fh->getCustomAttributes('opt-icn')}
                  class="opt-icn {$fh->getCustomClasses('opt-icn')}"
                  aria-hidden="true"
                  alt="{$fh->esc_attr($opt->lbl)}"
                  src="{$fh->esc_url($opt->img)}"
                >
OPTIMAGE;
            }
            $optionsList .= <<<OPTIONS
            <li
              {$fh->getCustomAttributes('option')}
              data-index="{$dataIndex}"
              data-value="{$fh->esc_attr($optVal)}"
              class="option {$fh->getCustomClasses('option')} {$disableOpt}"
              role="option"
              aria-selected="false"
              tabIndex="-1"
            >
              <span 
                {$fh->getCustomAttributes('opt-lbl-wrp')}
                class="opt-lbl-wrp {$fh->getCustomClasses('opt-lbl-wrp')}"
              >
                {$optImage}
                <span 
                  {$fh->getCustomAttributes('opt-lbl')}
                  class="opt-lbl {$fh->getCustomClasses('opt-lbl')}" 
                >
                  {$fh->kses_post($opt->lbl)}
                </span>
              </span>
              <span class="opt-prefix" />
            </li>
OPTIONS;
          }
        }
        $optionsList .= '</ul>';
        $dataIndex++;
      }

      $optionsList .= <<<OPTIONSLIST
        <ul
          {$fh->getCustomAttributes('option-list')}
          class="{$fh->getClassWithFieldKey('option-list')} {$fh->getConversationalCls('dpd-option-list')} {$fh->getCustomClasses('option-list')} active-list"
            aria-hidden="true"
            aria-label="Option List"
            tabIndex="-1"
            role="listbox"
          >
        </ul>
OPTIONSLIST;

      $multiChipClass = ($fh->property_exists_nested($field, 'config->multipleSelect', true) && $fh->property_exists_nested($field, 'config->showChip', true)) ? 'multi-chip' : '';

      return <<<DROPDOWNFIELD
    <div class="{$fh->getConversationalMultiCls('dpd-fld-container')} {$fh->getCustomClasses('dpd-fld-container')}">
      <div
        {$fh->getCustomAttributes('dpd-fld-wrp')}
        class="{$fh->getClassWithFieldKey('dpd-fld-wrp')} {$fh->getConversationalCls('dpd-slct-fld-wrp')} {$fh->getCustomClasses('dpd-fld-wrp')} {$readonlyCls} {$disabledCls}"
      >
        <input
          {$name}
          {$req}
          type="text"
          title="Dropdown Hidden Input"
          class="{$fh->getConversationalMultiCls('dpd-hidden-input')} d-none"
          {$fh->disabled()}
          {$fh->readonly()}
          {$val}
        />
        <div 
          {$fh->getCustomAttributes('dpd-wrp')}
          class="{$fh->getClassWithFieldKey('dpd-wrp')} {$fh->getConversationalCls('dpd-slct-wrp')} {$fh->getCustomClasses('dpd-wrp')}"
          role="combobox"
          aria-controls=""
          aria-live="assertive"
          aria-expanded="false"
          tabIndex="0"
          aria-label="Dropdown"
        >
          <div
            {$fh->getCustomAttributes('selected-opt-wrp')}
            class="{$fh->getConversationalMultiCls('selected-opt-wrp')} {$fh->getCustomClasses('selected-opt-wrp')}"
          >
          {$selectedOptImage}
                <span
                  {$fh->getCustomAttributes('selected-opt-lbl')}
                  aria-label="Selected Option Label"
                  class="{$fh->getConversationalMultiCls('selected-opt-lbl')} {$multiChipClass} {$fh->getCustomClasses('selected-opt-lbl')}"
                >
                  {$fh->esc_html($ph)}
                </span>
          </div>
          <div
            {$fh->getCustomAttributes('dpd-btn-wrp')}
            class="{$fh->getConversationalMultiCls('dpd-btn-wrp')} {$fh->getCustomClasses('dpd-btn-wrp')}"
          >
            <button
              {$fh->getCustomAttributes('selected-opt-clear-btn')}
              type="button"
              aria-label="Clear selected option value"
              class="{$fh->getConversationalMultiCls('selected-opt-clear-btn')} {$fh->getCustomClasses('selected-opt-clear-btn')}"
            >
              <svg
                width="15"
                height="15"
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
            <div 
              {$fh->getCustomAttributes('dpd-down-btn')}
              class="{$fh->getConversationalMultiCls('dpd-down-btn')} {$fh->getCustomClasses('dpd-down-btn')}"
            >
              <svg
                width="15"
                height="15"
                viewBox="0 0 24 24"
                title="Down icon"
                role="img"
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
        </div>
        <div 
          {$fh->getCustomAttributes('option-wrp')}
          class="{$fh->getConversationalMultiCls('option-wrp')} {$fh->getCustomClasses('option-wrp')}"
        >
          <div 
            {$fh->getCustomAttributes('option-inner-wrp')}
            class="{$fh->getConversationalMultiCls('option-inner-wrp')} {$fh->getCustomClasses('option-inner-wrp')}"
          >
            <div 
              {$fh->getCustomAttributes('option-search-wrp')}
              class="{$fh->getConversationalMultiCls('option-search-wrp')} {$fh->getCustomClasses('option-search-wrp')}"
            >
              <input
                {$fh->getCustomAttributes('opt-search-input')}
                type="search"
                class="{$fh->getConversationalMultiCls('opt-search-input')} {$fh->getCustomClasses('opt-search-input')}"
                placeholder="Search Country"
                aria-label="Search Options"
                aria-hidden="true"
                tabIndex="-1"
              />
              <svg
                {$fh->getCustomAttributes('opt-search-icn')}
                class="{$fh->getConversationalMultiCls('icn')} {$fh->getConversationalMultiCls('opt-search-icn')} {$fh->getCustomClasses('opt-search-icn')}"
                aria-hidden="true"
                width="22"
                height="22"
                role="img"
                title="Search icon"
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
              <button
                {$fh->getCustomAttributes('search-clear-btn')}
                type="button"
                aria-label="Clear search"
                class="{$fh->getConversationalMultiCls('icn')} {$fh->getConversationalMultiCls('search-clear-btn')} {$fh->getCustomClasses('search-clear-btn')}"
                tabIndex="-1"
              >
                <svg
                  width="13"
                  height="13"
                  role="img"
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
            </div>
            {$optionsList}
          </div>
        </div>
      </div>
    </div>
DROPDOWNFIELD;
    }
  }
}
