<?php

use BitCode\BitForm\Core\Fallback\FallBack;

/*
 * Add fallbacks
 * only class working under BitCode\BitForm\Core\Fallback\.
 */
FallBack::add('1.0.4', 'FormEntryMetaChange@changeMeta');
FallBack::add('1.4.1', 'Field@addButton');
FallBack::add('1.4.7', 'Validation@errorMessage');
FallBack::add('1.4.11', 'Field@buttonValidsObject');
FallBack::add('1.4.12.2', 'Validation@email');
FallBack::add('1.5.2', 'Report@report');
FallBack::add('1.6.2', 'WorkFlow@conditionalLogic');
Fallback::add('2.0.10', 'Field@phoneNumberMissingPattern');
Fallback::add('2.0.10', 'FormFallback@resetJsGeneratedPageIds');
Fallback::add('2.6.1', 'EntryMetaFallback@removeAllDuplicateMetaKeysExceptLast');
Fallback::add('2.7.2', 'StylesFallback@signatureFldIframeStyle');
Fallback::add('2.9.0', 'Report@addHiddenColumns');
Fallback::add('2.9.1', 'StylesFallback@stripeFldButtonDisabled');
Fallback::add('2.9.2', 'StylesFallback@stripeFldErrorMessage');
FallBack::add('2.10.1', 'FormFallback@resetJsGeneratedPageIds');
FallBack::add('2.11.1', 'FormFallback@v1formMigragion');
FallBack::add('2.16.4', 'StylesFallback@addStaticStyleForMultiStepForm');
// FallBack::add('2.16.0', 'AppSettingsFallback@addAutoUpdateSettings');
