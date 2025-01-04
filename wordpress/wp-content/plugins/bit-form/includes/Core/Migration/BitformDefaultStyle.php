<?php

namespace BitCode\BitForm\Core\Migration;

class BitformDefaultStyle
{
  public static function commonStyleClasses()
  {
    return [
      ':root' => [
        '--global-fld-bdr'=> 'solid',
        '--g-bdr-rad'     => '11px',
        '--g-bdr-width'   => '1px',
        '--g-font-family' => 'inherit',
        '--dir'           => 'ltr',
        '--inp-wrp-width' => '',
        '--lbl-al'        => '',
        '--fld-p'         => '10px',
        '--fld-m'         => '',
        '--fld-fs'        => '14px',

        '--fld-wrp-dis'      => 'block',
        '--fld-wrp-fdir'     => '',
        '--fld-wrp-justify'  => 'center',
        '--fld-wrp-m'        => '',
        '--fld-wrp-p'        => '7px 10px',
        '--fld-wrp-bdr'      => '',
        '--fld-wrp-bdr-width'=> '',
        '--fld-wrp-bdr-rad'  => '',
        '--fld-wrp-width'    => '',

        '--lbl-wrp-sa'       => '',
        '--lbl-wrp-width'    => '100%',
        '--lbl-wrp-m'        => '0 0 5px 0',
        '--lbl-wrp-p'        => '',
        '--lbl-wrp-bdr'      => '',
        '--lbl-wrp-bdr-width'=> '',
        '--lbl-wrp-bdr-rad'  => '',
        '--lbl-font-w'       => 500,
        '--lbl-font-style'   => '',

        '--fld-lbl-m'        => '',
        '--fld-lbl-p'        => '',
        '--fld-lbl-fs'       => '1rem',
        '--fld-lbl-bdr'      => '',
        '--fld-lbl-bdr-width'=> '',
        '--fld-lbl-bdr-rad'  => '',
        '--fld-lbl-pn'       => '',
        '--fld-f-w'          => 500,
        '--fld-f-style'      => 'normal',

        '--req-smbl-m' => '',
        '--req-smbl-p' => '',
        '--req-smbl-fs'=> '',
        '--req-smbl-fw'=> '',
        '--req-smbl-lh'=> '',
        '--req-smbl-pn'=> '',
        '--req-smbl-lt'=> '',
        '--req-smbl-rt'=> '',

        '--sub-titl-m'         => '2px 0px 0px 0px',
        '--sub-titl-p'         => '3px 0',
        '--sub-titl-al'        => '',
        '--sub-titl-fs'        => '12px',
        '--sub-titl-bdr'       => '',
        '--sub-titl-bdr-width' => '',
        '--sub-titl-bdr-rad'   => '',
        '--sub-titl-font-w'    => 500,
        '--sub-titl-font-style'=> '',

        '--hlp-txt-m'         => '2px 0px 0px 0px',
        '--hlp-txt-p'         => '3px 0',
        '--hlp-txt-fs'        => '12px',
        '--hlp-txt-al'        => '',
        '--hlp-txt-bdr'       => '',
        '--hlp-txt-bdr-width' => '',
        '--hlp-txt-bdr-rad'   => '',
        '--hlp-txt-font-w'    => 400,
        '--hlp-txt-font-style'=> '',

        '--err-m'             => '5px 0 0 0',
        '--err-p'             => '10px',
        '--err-bdr'           => 'solid',
        '--err-bdr-width'     => '1px',
        '--err-bdr-rad'       => '8px',
        '--err-txt-al'        => '',
        '--err-txt-fs'        => '12px',
        '--err-txt-font-w'    => 400,
        '--err-txt-font-style'=> '',
        '--err-h'             => '',

        '--pre-i-h'        => '25px',
        '--pre-i-w'        => '25px',
        '--pre-i-m'        => '0px 0px 0px 3px',
        '--pre-i-p'        => '',
        '--pre-i-bdr'      => '',
        '--pre-i-bdr-width'=> '',
        '--pre-i-bdr-rad'  => '8px',

        '--suf-i-h'        => '25px',
        '--suf-i-w'        => '25px',
        '--suf-i-m'        => '0px 3px 0px 0px',
        '--suf-i-p'        => '',
        '--suf-i-bdr'      => '',
        '--suf-i-bdr-width'=> '',
        '--suf-i-bdr-rad'  => '8px',

        '--lbl-pre-i-h'        => '20px',
        '--lbl-pre-i-w'        => '20px',
        '--lbl-pre-i-m'        => '0px 5px 0px 0px',
        '--lbl-pre-i-p'        => '',
        '--lbl-pre-i-bdr'      => '',
        '--lbl-pre-i-bdr-width'=> '',
        '--lbl-pre-i-bdr-rad'  => '8px',

        '--lbl-suf-i-h'        => '20px',
        '--lbl-suf-i-w'        => '20px',
        '--lbl-suf-i-m'        => '0px 0px 0px 5px',
        '--lbl-suf-i-p'        => '',
        '--lbl-suf-i-bdr'      => '',
        '--lbl-suf-i-bdr-width'=> '',
        '--lbl-suf-i-bdr-rad'  => '8px',

        '--sub-titl-pre-i-h'        => '15px',
        '--sub-titl-pre-i-w'        => '15px',
        '--sub-titl-pre-i-m'        => '0px 5px 0px 0px',
        '--sub-titl-pre-i-p'        => '',
        '--sub-titl-pre-i-bdr'      => '',
        '--sub-titl-pre-i-bdr-width'=> '',
        '--sub-titl-pre-i-bdr-rad'  => '8px',

        '--sub-titl-suf-i-h'        => '15px',
        '--sub-titl-suf-i-w'        => '15px',
        '--sub-titl-suf-i-m'        => '0px 0px 0px 5px',
        '--sub-titl-suf-i-p'        => '',
        '--sub-titl-suf-i-bdr'      => '',
        '--sub-titl-suf-i-bdr-width'=> '',
        '--sub-titl-suf-i-bdr-rad'  => '8px',

        '--hlp-txt-pre-i-h'        => '15px',
        '--hlp-txt-pre-i-w'        => '15px',
        '--hlp-txt-pre-i-m'        => '0px 5px 0px 0px',
        '--hlp-txt-pre-i-p'        => '',
        '--hlp-txt-pre-i-bdr'      => '',
        '--hlp-txt-pre-i-bdr-width'=> '',
        '--hlp-txt-pre-i-bdr-rad'  => '8px',

        '--hlp-txt-suf-i-h'        => '15px',
        '--hlp-txt-suf-i-w'        => '15px',
        '--hlp-txt-suf-i-m'        => '0px 0px 0px 5px',
        '--hlp-txt-suf-i-p'        => '',
        '--hlp-txt-suf-i-bdr'      => '',
        '--hlp-txt-suf-i-bdr-width'=> '',
        '--hlp-txt-suf-i-bdr-rad'  => '8px',

        '--err-txt-pre-i-h'        => '15px',
        '--err-txt-pre-i-w'        => '15px',
        '--err-txt-pre-i-m'        => '0px 5px 0px 0px',
        '--err-txt-pre-i-p'        => '',
        '--err-txt-pre-i-bdr'      => '',
        '--err-txt-pre-i-bdr-width'=> '',
        '--err-txt-pre-i-bdr-rad'  => '8px',

        '--err-txt-suf-i-h'        => '15px',
        '--err-txt-suf-i-w'        => '15px',
        '--err-txt-suf-i-m'        => '0px 0px 0px 5px',
        '--err-txt-suf-i-p'        => '',
        '--err-txt-suf-i-bdr'      => '',
        '--err-txt-suf-i-bdr-width'=> '',
        '--err-txt-suf-i-bdr-rad'  => '8px',

        '--btn-fs'       => 'var(--fld-fs)',
        '--btn-p'        => '13px 20px',
        '--btn-m'        => '0px 0px',
        '--btn-fw'       => 700,
        '--btn-f-style'  => '',
        '--btn-bdr'      => 'none',
        '--btn-bdr-rad'  => 'var(--g-bdr-rad)',
        '--btn-bdr-width'=> '0',

        '--global-outline'       => '1px solid var(--global-accent-color)',
        '--global-outline-offset'=> '1px',
        '--g-o-offset'           => '1px',
        '--g-o-w'                => '1px',
        '--g-o-s'                => 'solid',

        '--global-accent-color'=> 'hsla(217, 100%, 50%, 100%)',
        '--gah'                => 217,
        '--gas'                => '100%',
        '--gal'                => '50%',
        '--gaa'                => '100%',
        '--global-font-color'  => 'hsla(0, 0%, 14%, 100%)',
        '--gfh'                => 0,
        '--gfs'                => '0%',
        '--gfl'                => '14%',
        '--gfa'                => '100%',
        '--global-bg-color'    => '',
        '--gbg-h'              => 0,
        '--gbg-s'              => '0%',
        '--gbg-l'              => '100%',
        '--gbg-a'              => '100%',
        '--global-fld-bdr-clr' => 'hsla(0, 0%, 67%, 100%)',
        '--gfbc-h'             => 0,
        '--gfbc-s'             => '0%',
        '--gfbc-l'             => '67%',
        '--gfbc-a'             => '100%',
        '--global-fld-bg-color'=> '',
        '--gfbg-h'             => 0,
        '--gfbg-s'             => '0%',
        '--gfbg-l'             => '100%',
        '--gfbg-a'             => '100%',

        '--fld-focs-i-fltr'=> 'invert(26%) sepia(41%) saturate(6015%) hue-rotate(211deg) brightness(100%) contrast(108%)',

        '--fld-wrp-bg'     => '',
        '--fld-wrp-bdr-clr'=> '',
        '--fld-wrp-sh'     => '',

        '--lbl-wrp-bg'     => '',
        '--lbl-wrp-sh'     => '',
        '--lbl-wrp-bdr-clr'=> '',

        '--fld-lbl-bg'     => '',
        '--fld-lbl-c'      => 'var(--global-font-color)',
        '--fld-lbl-sh'     => '',
        '--fld-lbl-bdr-clr'=> '',

        '--req-smbl-c'=> 'var(--global-font-color)',

        '--sub-titl-bg'     => '',
        '--sub-titl-c'      => 'hsla(var(--gfh), var(--gfs), var(--gfl), 0.7)',
        '--sub-titl-sh'     => '',
        '--sub-titl-bdr-clr'=> '',

        '--fld-inp-c'=> 'var(--global-font-color)',

        '--hlp-txt-bg'     => '',
        '--hlp-txt-c'      => 'hsla(var(--gfh), var(--gfs), var(--gfl), 0.7)',
        '--hlp-txt-sh'     => '',
        '--hlp-txt-bdr-clr'=> '',

        '--pre-i-clr'    => '',
        '--pre-i-fltr'   => '',
        '--pre-i-sh'     => '',
        '--pre-i-bdr-clr'=> '',

        '--suf-i-clr'    => '',
        '--suf-i-fltr'   => '',
        '--suf-i-sh'     => '',
        '--suf-i-bdr-clr'=> '',

        '--lbl-pre-i-clr'    => '',
        '--lbl-pre-i-fltr'   => '',
        '--lbl-pre-i-sh'     => '',
        '--lbl-pre-i-bdr-clr'=> '',

        '--lbl-suf-i-clr'    => '',
        '--lbl-suf-i-fltr'   => '',
        '--lbl-suf-i-sh'     => '',
        '--lbl-suf-i-bdr-clr'=> '',

        '--sub-titl-pre-i-clr'    => '',
        '--sub-titl-pre-i-fltr'   => '',
        '--sub-titl-pre-i-sh'     => '',
        '--sub-titl-pre-i-bdr-clr'=> '',

        '--sub-titl-suf-i-clr'    => '',
        '--sub-titl-suf-i-fltr'   => '',
        '--sub-titl-suf-i-sh'     => '',
        '--sub-titl-suf-i-bdr-clr'=> '',

        '--hlp-txt-pre-i-clr'    => '',
        '--hlp-txt-pre-i-fltr'   => '',
        '--hlp-txt-pre-i-sh'     => '',
        '--hlp-txt-pre-i-bdr-clr'=> '',

        '--hlp-txt-suf-i-clr'    => '',
        '--hlp-txt-suf-i-fltr'   => '',
        '--hlp-txt-suf-i-sh'     => '',
        '--hlp-txt-suf-i-bdr-clr'=> '',

        '--err-txt-pre-i-clr'    => '',
        '--err-txt-pre-i-fltr'   => '',
        '--err-txt-pre-i-sh'     => '',
        '--err-txt-pre-i-bdr-clr'=> '',

        '--err-txt-suf-i-clr'    => '',
        '--err-txt-suf-i-fltr'   => '',
        '--err-txt-suf-i-sh'     => '',
        '--err-txt-suf-i-bdr-clr'=> '',

        '--err-bg'     => 'hsla(0, 100%, 94%, 100%)',
        '--err-c'      => 'hsla(0, 100%, 11%, 100%)',
        '--err-sh'     => '',
        '--err-bdr-clr'=> 'hsla(0, 50%, 90%, 100%)',

        '--btn-bg'     => 'var(--global-accent-color)',
        '--btn-bgc'    => 'var(--global-accent-color)',
        '--btn-c'      => 'hsla(0, 0%, 100%, 100%)',
        '--btn-bdr-clr'=> 'none',
        '--btn-sh'     => '0   2px 4px -2px hsla(0, 0%, 0%, 40%)',

        '--ck-bdr-c'=> 'hsla(210, 78%, 96%, 100%)',

        '--g-o-c'=> 'hsla(217, 100%, 50%, 100%)',

        '--bf-lower-track-clr'=> 'var(--global-accent-color)',
        '--bf-upper-track-clr'=> '#ffffff',

        '--bg-0'  => 'hsl(0, 0%, 100%)',
        '--bg-5'  => 'hsl(0, 0%, 95%)',
        '--bg-10' => 'hsl(0, 0%, 90%)',
        '--bg-15' => 'hsl(0, 0%, 85%)',
        '--bg-20' => 'hsl(0, 0%, 80%)',
        '--bg-25' => 'hsl(0, 0%, 75%)',
        '--bg-30' => 'hsl(0, 0%, 70%)',
        '--bg-35' => 'hsl(0, 0%, 65%)',
        '--bg-40' => 'hsl(0, 0%, 60%)',
        '--bg-45' => 'hsl(0, 0%, 55%)',
        '--bg-50' => 'hsl(0, 0%, 50%)',
        '--bg-55' => 'hsl(0, 0%, 45%)',
        '--bg-60' => 'hsl(0, 0%, 40%)',
        '--bg-65' => 'hsl(0, 0%, 35%)',
        '--bg-70' => 'hsl(0, 0%, 30%)',
        '--bg-75' => 'hsl(0, 0%, 25%)',
        '--bg-80' => 'hsl(0, 0%, 20%)',
        '--bg-85' => 'hsl(0, 0%, 15%)',
        '--bg-90' => 'hsl(0, 0%, 10%)',
        '--bg-95' => 'hsl(0, 0%, 5%)',
        '--bg-100'=> 'hsl(0, 0%, 0%)',
      ],
      '.bf-fld-wrp' => [
        'display'       => 'var(--fld-wrp-dis, block)',
        'flex-direction'=> 'var(--fld-wrp-fdir, row)',

        'background'   => 'var(--fld-wrp-bg, transparent)',
        'padding'      => 'var(--fld-wrp-p, 0)',
        'margin'       => 'var(--fld-wrp-m, 0)',
        'position'     => 'relative',
        'box-shadow'   => 'var(--fld-wrp-sh, none)',
        'border-style' => 'var(--fld-wrp-bdr, medium)',
        'border-color' => 'var(--fld-wrp-bdr-clr, none)',
        'border-radius'=> 'var(--fld-wrp-bdr-rad, 0)',
        'border-width' => 'var(--fld-wrp-bdr-width, 0)',
        'width'        => 'var(--fld-wrp-width)',
      ],
      '.bf-lbl-wrp'=> [
        'width'           => 'var(--lbl-wrp-width, auto)',
        'align-self'      => 'var(--lbl-wrp-sa, auto)',
        'margin'          => 'var(--lbl-wrp-m, 0)',
        'padding'         => 'var(--lbl-wrp-p, 0)',
        'background-color'=> 'var(--lbl-wrp-bg, none)',
        'box-shadow'      => 'var(--lbl-wrp-sh, none)',
        'border-style'    => 'var(--lbl-wrp-bdr, medium)',
        'border-color'    => 'var(--lbl-wrp-bdr-clr, none)',
        'border-radius'   => 'var(--lbl-wrp-bdr-rad, 0)',
        'border-width'    => 'var(--lbl-wrp-bdr-width, 0)',
      ],
      '.bf-lbl'=> [
        'background-color'=> 'var(--fld-lbl-bg, none)',
        'color'           => 'var(--fld-lbl-c, inherit)',
        'font-size'       => 'var(--fld-lbl-fs)',
        'display'         => 'flex',
        'align-items'     => 'center',

        'justify-content'=> 'var(--lbl-al, initial)',
        'margin'         => 'var(--fld-lbl-m, 0)',
        'padding'        => 'var(--fld-lbl-p, 0)',
        'box-shadow'     => 'var(--fld-lbl-sh, none)',
        'border-style'   => 'var(--fld-lbl-bdr, medium)',
        'border-color'   => 'var(--fld-lbl-bdr-clr, none)',
        'border-radius'  => 'var(--fld-lbl-bdr-rad, 0)',
        'border-width'   => 'var(--fld-lbl-bdr-width, 0)',
        'width'          => '100%',
        'font-weight'    => 'var(--lbl-font-w)',
        'font-style'     => 'var(--lbl-font-style)',
        'position'       => 'var(--fld-lbl-pn,unset)',
      ],
      '.bf-req-smbl'=> [
        'color'      => 'var(--req-smbl-c, inherit)',
        'font-size'  => 'var(--req-smbl-fs)',
        'margin'     => 'var(--req-smbl-m, 0)',
        'padding'    => 'var(--req-smbl-p, 0)',
        'font-weight'=> 'var(--req-smbl-fw)',
        'line-height'=> 'var(--req-smbl-lh, 12px)',
        'position'   => 'var(--req-smbl-pn,unset)',
        'right'      => 'var(--req-smbl-rt,unset)',
        'left'       => 'var(--req-smbl-lt,unset)',
      ],
      '.bf-inp-wrp'=> ['width'=> 'var(--inp-wrp-width, auto)'],

      '.bf-inp-fld-wrp'=> ['position'=> 'relative', 'margin'=> 'var(--fld-m, 0)'],
      '.bf-fld'        => [
        'display'         => 'block',
        'direction'       => 'inherit !important',
        'font-family'     => 'inherit',
        'width'           => '100% !important',
        'outline'         => 'none !important',
        'background-color'=> 'var(--global-fld-bg-color) !important',
        'border-style'    => 'var(--global-fld-bdr) !important',
        'border-color'    => 'var(--global-fld-bdr-clr) !important',
        'border-radius'   => 'var(--g-bdr-rad) !important',
        'border-width'    => 'var(--g-bdr-width) !important',
        'font-size'       => 'var(--fld-fs) !important',
        'font-weight'     => 'var(--fld-f-w) !important',
        'font-style'      => 'var(--fld-f-style) !important',
        'color'           => 'var(--fld-inp-c) !important',
        'padding'         => 'var(--fld-p)!important',
        'margin'          => 'var(--fld-m)!important',
        'line-height'     => '1.4 !important',
        'height'          => '40px',
        'resize'          => 'vertical',
      ],
      'input[type="color" i][list]::-webkit-color-swatch' => [
        'border'       => 0,
        'border-radius'=> '2px'
      ],
      '.bf-fld:focus'=> [
        'box-shadow'  => '0 0 0 3px hsla(var(--gah), var(--gas), var(--gal), 0.30) !important',
        'border-color'=> 'var(--global-accent-color) !important',
      ],
      '.bf-fld:hover'   => ['border-color'=> 'var(--global-accent-color) !important'],
      '.bf-fld:disabled'=> [
        'cursor'          => 'default',
        'background-color'=> 'var(--bg-5)!important',
        'color'           => 'hsla(var(--gfh), var(--gfs), var(--gfl), .5) !important',
        'border-color'    => 'var(--bg-5) !important',
      ],
      '.bf-fld:read-only'=> [
        'cursor'=> 'default',
      ],
      '.bf-fld::placeholder'=> [
        'font-family'=> 'inherit',
        'color'      => 'hsla(var(--gfh), var(--gfs), var(--gfl), 40%) !important',
      ],
      '.bf-cks'=> [
        'position'      => 'absolute',
        'width'         => 0,
        'height'        => 0,
        'pointer-events'=> 'none',
        'user-select'   => 'none',
      ],

      '.bf-cc'=> [

        'display'              => 'grid',
        'grid-template-columns'=> '1fr',
        'width'                => '100%',
        'grid-row-gap'         => '5px',
        'column-gap'           => '10px',
      ],

      '.bf-cl'=> [
        'cursor'     => 'pointer',
        'display'    => 'flex',
        'align-items'=> 'center',
        'color'      => 'var(--fld-inp-c)',
        'padding'    => '5px',
      ],
      '.bf-ct'=> [
        'font-size'  => 'var(--fld-fs)',
        'font-weight'=> 'var(--fld-f-w)',
        'font-style' => 'var(--fld-f-style)',
      ],

      '.bf-ci'=> [
        'position'=> 'absolute',
        'opacity' => '0!important',
      ],
      '.bf-ci:checked ~ [data-cl] [data-bx]'=> [
        'background'  => 'var(--global-accent-color)',
        'border-color'=> 'var(--global-accent-color)',
      ],
      '.bf-ci:hover ~ [data-cl] [data-bx]'=> [
        'border-color'=> 'var(--global-accent-color)',
      ],
      '.bf-ci:checked ~ [data-oinp-wrp]'          => ['display'=> 'block !important'],
      '.bf-ci:focus ~ [data-cl] [data-bx]'        => ['box-shadow'=> '0 0 0 3px hsla(var(--gah), var(--gas), var(--gal), 0.3)'],
      '.bf-ci:focus-visible ~ [data-cl] [data-bx]'=> [
        'outline'       => '2px solid var(--global-accent-color)',
        'outline-offset'=> '2px',
        'transition'    => 'outline-offset 0.2s ease',
      ],
      '.bf-ci:active:focus-visible ~ [data-cl] [data-bx]'=> [
        'outline-offset'=> 0,
      ],
      '.bf-ci:active ~ [data-cl] [data-bx]'=> ['transform'=> 'scale(0.9)'],
      '.bf-ci:disabled ~ [data-cl]'        => [
        'opacity'       => 0.6,
        'pointer-events'=> 'none',
        'cursor'        => 'not-allowed',
      ],
      '.bf-bx'=> [
        'position'    => 'relative',
        'height'      => '18px',
        'width'       => '18px',
        'border-color'=> 'hsla(var(--gfh), var(--gfs), var(--gfl), 0.7)',
        'border-style'=> 'solid',
        'border-width'=> '2px',
        'display'     => 'inline-flex',
        'margin'      => '0 10px 0 0',

        'transition'     => 'all 0.2s',
        'justify-content'=> 'center',
        'align-items'    => 'center',
        'border-radius'  => '50%',
      ],
      '.bf-other-inp-wrp'=> ['display'=> 'none'],
      '.bf-other-inp'    => [
        'display'  => 'inline-block !important',
        'direction'=> 'inherit !important',

        'margin'          => '0px 0px 0px 33px',
        'width'           => 'calc(100% - 33px) !important',
        'outline'         => 'none !important',
        'background-color'=> 'var(--global-fld-bg-color) !important',
        'border-style'    => 'var(--global-fld-bdr) !important',
        'border-color'    => 'var(--global-fld-bdr-clr) !important',
        'border-radius'   => 'var(--g-bdr-rad) !important',
        'border-width'    => 'var(--g-bdr-width) !important',
        'font-size'       => 'var(--fld-fs) !important',
        'font-weight'     => 'var(--fld-f-w) !important',
        'font-style'      => 'var(--fld-f-style) !important',
        'color'           => 'var(--fld-inp-c) !important',
        'padding'         => '10px 8px !important',
        'line-height'     => '1.4 !important',

        'height'=> '40px',
      ],
      '.bf-other-inp:focus'=> [
        'box-shadow'  => '0 0 0 3px hsla(var(--gah), var(--gas), var(--gal), 0.30) !important',
        'border-color'=> 'var(--global-accent-color) !important',
      ],
      '.bf-other-inp:hover'=> ['border-color'=> 'var(--global-accent-color) !important'],

      '.bf-svgwrp'=> [
        'height'=> '12px',
        'width' => '10px',
        'filter'=> 'drop-shadow(0px 1px 1px hsl(var(--gah), var(--gas), 45%))',
      ],
      '.bf-ck-icn'    => ['stroke-dashoffset'=> '16px'],
      '.bf-ck-svgline'=> [
        'stroke'          => 'white',
        'fill'            => 'none',
        'stroke-linecap'  => 'round',
        'stroke-linejoin' => 'round',
        'stroke-width'    => '2px',
        'stroke-dasharray'=> '16px',
      ],
      '.bf-ci:checked ~ [data-cl] [data-ck-icn]'=> ['stroke-dashoffset'=> 0],

      '.bf-bx::before'=> [
        'content' => '""',
        'position'=> 'absolute',
        'left'    => '50%',
        'height'  => 0,
        'width'   => 0,

        'border-radius'=> '50%',
        'transition'   => 'all 0.2s cubic-bezier(0.25, 0.1, 0.59, 1.82)',
        'top'          => '50%',
        'background'   => 'white',
        'box-shadow'   => '0 1px 3px 0px grey',
        'transform'    => 'translate(-50%, -50%)',
      ],
      '.bf-ci:checked ~ [data-cl] [data-bx]::before'=> [
        'width' => '50%',
        'height'=> '50%',
      ],
      '.bf-btn'=> [
        'font-size'=> 'var(--btn-fs)!important',
        'padding'  => 'var(--btn-p)!important',

        'background'     => 'var(--btn-bg)',
        'color'          => 'var(--btn-c)',
        'font-weight'    => 'var(--btn-fw)',
        'border-style'   => 'var(--btn-bdr)',
        'border-color'   => 'var(--btn-bdr-clr)',
        'border-width'   => 'var(--btn-bdr-width)',
        'border-radius'  => 'var(--btn-bdr-rad) !important',
        'box-shadow'     => 'var(--btn-sh)',
        'cursor'         => 'pointer',
        'font-family'    => 'inherit',
        'font-style'     => 'var(--btn-f-style)',
        'line-height'    => '1',
        'margin'         => 'var(--btn-m)',
        'outline'        => 'none',
        'display'        => 'flex',
        'justify-content'=> 'center',
        'align-items'    => 'center',
        'transition'     => 'background-color 0.2s, transform 0.2s',
      ],
      '.bf-btn:hover'=> [
        'background-color'=> 'hsl(var(--gah), var(--gas), calc(var(--gal) - 5%)) !important',
      ],
      '.bf-btn:active'=> [
        'transform'=> 'scale(0.95)',
      ],
      '.bf-btn:focus-visible'=> [
        'outline'       => '2px solid var(--global-accent-color)',
        'outline-offset'=> '2px',
        'transition'    => 'outline-offset 0.2s ease',
      ],
      '.bf-btn:active:focus-visible'=> [
        'outline-offset'=> 0,
      ],
      '.bf-btn:disabled'=> [
        'cursor'        => 'not-allowed',
        'pointer-events'=> 'none',
        'opacity'       => '0.5',
      ],

      '.bf-country-fld-container'=> [
        'position'=> 'relative',
        'height'  => '40px',
        'width'   => '100%',
        'display' => 'inline-block',
      ],

      '.bf-country-fld-wrp'=> [
        'position'        => 'absolute',
        'width'           => '100%',
        'background-color'=> 'var(--global-fld-bg-color)',
        'border-style'    => 'var(--global-fld-bdr) !important',
        'border-color'    => 'var(--global-fld-bdr-clr) !important',
        'border-radius'   => 'var(--g-bdr-rad) !important',
        'border-width'    => 'var(--g-bdr-width) !important',
        'font-size'       => 'var(--fld-fs) !important',
        'font-weight'     => 'var(--fld-f-w) !important',
        'font-style'      => 'var(--fld-f-style) !important',

        'color'         => 'var(--fld-inp-c) !important',
        'overflow'      => 'hidden',
        'display'       => 'flex',
        'flex-direction'=> 'column',
        'transition'    => 'box-shadow .2s',
      ],

      '.bf-country-fld-wrp.disabled .bf-dpd-wrp'=> [
        'cursor'          => 'not-allowed',
        'pointer-events'  => 'none',
        'background-color'=> 'hsla(var(--gfbg-h), var(--gfbg-s), calc(var(--gfbg-l) + 20%), var(--gfbg-a))',
        'color'           => 'hsla(var(--gfh), var(--gfs), calc(var(--gfl) + 40%), var(--gfa))',
        'border-color'    => 'hsla(var(--gfbc-h), var(--gfbc-s), calc(var(--gfbc-l) + 20%), var(--gfbc-a))',
      ],
      '.bf-country-fld-wrp.readonly .bf-dpd-wrp'=> [
        'pointer-events'  => 'none',
        'background-color'=> 'hsla(var(--gfbg-h), var(--gfbg-s), calc(var(--gfbg-l) + 20%), var(--gfbg-a))',
        'color'           => 'hsla(var(--gfh), var(--gfs), calc(var(--gfl) + 40%), var(--gfa))',
        'border-color'    => 'hsla(var(--gfbc-h), var(--gfbc-s), calc(var(--gfbc-l) + 20%), var(--gfbc-a))',
      ],

      '.bf-country-fld-wrp:hover:not(.menu-open):not(.disabled)'=> ['border-color'=> 'var(--global-accent-color) !important'],

      '.bf-country-fld-wrp:focus-within:not(.menu-open):not(.disabled)'=> [
        'box-shadow'  => '0 0 0 3px hsla(var(--gah), var(--gas), var(--gal), 0.30) !important',
        'border-color'=> 'var(--global-accent-color) !important',
      ],

      '.bf-country-fld-wrp.menu-open'=> [
        'background-color'=> 'var(--bg-0)',
        'z-index'         => '999',
        'box-shadow'      => '0px 1.2px 2.2px hsla(0, 0%, 0%, 3.2%),
          0px 2.9px 5.3px hsla(0, 0%, 0%, 4.5%),
          0px 5.4px 10px hsla(0, 0%, 0%, 5.4%),
          0px 9.6px 17.9px hsla(0, 0%, 0%, 6.2%),
          0px 18px 33.4px hsla(0, 0%, 0%, 7.3%),
          0px 43px 80px hsla(0, 0%, 0%, 10%)',
        'border-color'=> 'var(--bg-10)!important',
      ],

      '.bf-dpd-wrp'=> [
        'background-color'=> 'transparent',
        'overflow'        => 'hidden',
        'font-weight'     => '500',
        'display'         => 'flex',
        'justify-content' => 'space-between',
        'align-items'     => 'center',
        'cursor'          => 'pointer',
        'height'          => '38px',
        'padding'         => '10px',
        'box-sizing'      => 'border-box',

        'position'=> 'relative',
        'outline' => 'none',
      ],

      '.bf-selected-country-wrp'=> [
        'height'     => '100%',
        'display'    => 'flex',
        'align-items'=> 'center',
      ],

      '.bf-selected-country-img'=> [
        'height'             => '17px !important',
        'width'              => '25px',
        'border-radius'      => '3px !important',
        'outline'            => '1px solid var(--bg-10)',
        'background-color'   => 'var(--bg-10)',
        'margin'             => '0 10px 0 0',
        '-webkit-user-select'=> 'none',
        'user-select'        => 'none',
      ],

      '.bf-inp-clr-btn'=> [
        'display'      => 'none',
        'right'        => '6px',
        'padding'      => '0px !important',
        'margin'       => '0',
        'background'   => 'transparent !important',
        'border'       => '0',
        'outline'      => '0',
        'cursor'       => 'pointer',
        'margin-right' => '5px',
        'place-content'=> 'center',
        'width'        => '16px',
        'height'       => '16px',
        'border-radius'=> '50% !important',
        'color'        => 'var(--fld-inp-c) !important',
      ],

      '.bf-inp-clr-btn:hover'=> [
        'background-color'=> 'var(--bg-15) !important',
      ],

      '.bf-dpd-btn-wrp'=> [
        'display'    => 'flex',
        'align-items'=> 'center',
      ],

      '.bf-option-list .opt-lbl'=> [],

      '.bf-option-wrp'=> [
        'max-height'    => '0px',
        'min-height'    => 'auto',
        'margin'        => 'auto',
        'width'         => '100%',
        'overflow'      => 'hidden',
        'display'       => 'flex',
        'flex-direction'=> 'column',
      ],

      '.bf-option-inner-wrp'=> [
        'overflow'      => 'hidden',
        'display'       => 'flex',
        'flex-direction'=> 'column',
      ],

      '.bf-option-list'=> [
        'padding'   => '0',
        'margin'    => '0 0 2px 0 !important',
        'height'    => '100%',
        'overflow-y'=> 'auto',

        /* firefox */
        'scrollbar-width'=> 'thin !important',
        'scrollbar-color'=> 'var(--bg-15) transparent !important',
      ],

      '.bf-option-list::-webkit-scrollbar'=> ['width'=> '8px'],

      '.bf-option-list::-webkit-scrollbar-thumb'=> [
        'background-color'=> 'var(--bg-15)',
        'border-radius'   => 'var(--g-bdr-rad)',
      ],

      '.bf-option-search-wrp'=> [
        'position'=> 'relative',
        'margin'  => '5px 5px 0 5px',
      ],

      '.bf-opt-search-icn'=> [
        'position' => 'absolute',
        'top'      => '50%',
        'transform'=> 'translateY(-50%)',
        'color'    => 'var(--bg-25) !important',
        'left'     => '13px',
      ],

      '.bf-opt-search-input'=> [
        'width'      => '100%',
        'padding'    => '0 5px 0 41px !important',
        'outline'    => 'none',
        'box-shadow' => 'none',
        'font-family'=> 'inherit',
        /* border-radius=> 8px, */
        'border'=> 'none !important',
        /* border-top=> 1px solid #ddd, */
        /* border-bottom=> 1px solid #ddd, */
        'background-color'=> 'var(--bg-5)',
        'height'          => '35px',
        'border-radius'   => 'calc(var(--g-bdr-rad) - 1px) !important',
        'font-size'       => '1rem',

        'color'=> 'var(--global-font-color) !important',
      ],

      '.bf-opt-search-input::placeholder'=> [
        'color'=> 'hsla(var(--gfh), var(--gfs), var(--gfl), 0.5)',
      ],
      '.bf-opt-search-input:focus'=> [
        'background-color'=> 'var(--bg-0)',
        'box-shadow'      => '0 0 0 2px var(--global-accent-color) inset',
      ],
      '.bf-opt-search-input:focus~svg'                         => ['color'=> 'var(--global-accent-color)!important'],
      '.bf-opt-search-input::-webkit-search-decoration'        => ['display'=> 'none'],
      '.bf-opt-search-input::-webkit-search-cancel-button'     => ['display'=> 'none'],
      '.bf-opt-search-input::-webkit-search-results-button'    => ['display'=> 'none'],
      '.bf-opt-search-input::-webkit-search-results-decoration'=> ['display'=> 'none'],

      /* border-radius   => 20px,
        background-color=> red, */

      '.bf-search-clear-btn'=> [
        'position'     => 'absolute',
        'top'          => '50%',
        'transform'    => 'translateY(-50%)',
        'display'      => 'none',
        'right'        => '6px',
        'padding'      => '0px !important',
        'margin'       => '0px',
        'background'   => 'var(--bg-25) !important',
        'border'       => '',
        'border-width' => '0px',
        'border-radius'=> '50% !important',
        'outline'      => 0,
        'cursor'       => 'pointer',
        'margin-right' => '5px !important',
        'place-content'=> 'center',
        'width'        => '16px',
        'height'       => '16px',
        'color'        => 'var(--bg-0)',
      ],

      '.bf-search-clear-btn:hover'=> [
        'background'=> 'var(--bg-50) !important',
      ],

      '.bf-search-clear-btn:focus-visible'=> [
        'box-shadow'=> '0 0 0 2px var(--global-accent-color)',
        'outline'   => 'none',
      ],

      '.bf-option-list .option'=> [
        'margin'         => '2px 5px !important',
        'transition'     => 'background 0.2s',
        'border-radius'  => 'calc(var(--g-bdr-rad) - 2px)',
        'font-size'      => 'calc(var(--fld-fs) - 2px)',
        'cursor'         => 'pointer',
        'text-align'     => 'left',
        'border'         => 'none',
        'padding'        => '8px 7px',
        'display'        => 'flex',
        'align-items'    => 'center',
        'justify-content'=> 'space-between',
      ],

      '.bf-option-list .option:hover:not(.selected-opt)'=> [
        'background-color'=> 'var(--bg-10)',
      ],

      '.bf-option-list .option:focus-visible'=> [
        'outline'         => '2px solid var(--global-accent-color)',
        'background-color'=> 'var(--bg-10)',
      ],

      '.bf-option-list .selected-opt'=> [
        'font-weight'     => 500,
        'color'           => '#fff',
        'background-color'=> 'var(--global-accent-color)',
      ],

      '.bf-option-list .selected-opt:focus-visible'=> [
        'background-color'=> 'var(--global-accent-color)',
      ],

      '.bf-option-list .opt-not-found'=> [
        'text-align'=> 'center',
        'list-style'=> 'none',
        'margin'    => '5px',
      ],

      '.bf-search-clear-btn:focus-visible'=> [
        'box-shadow'=> '0 0 0 1.5px hsla(240, 100%, 50%, 100%) inset',
        'outline'   => 'none',
      ],

      '.bf-inp-clr-btn:focus-visible'=> [
        'box-shadow'=> '0 0 0 1.5px hsla(240, 100%, 50%, 100%) inset',
        'outline'   => 'none',
      ],

      '.bf-option-list .opt-lbl-wrp'=> [
        'display'    => 'flex',
        'align-items'=> 'center',
      ],

      '.bf-option-list .opt-icn'=> [
        'margin'             => '0 10px 0 0',
        'height'             => '17px',
        'width'              => '25px',
        'border-radius'      => '3px',
        'box-shadow'         => '0 0 0 1px var(--bg-5)',
        '-webkit-user-select'=> 'none',
        'user-select'        => 'none',
      ],

      '.bf-dpd-down-btn'=> [
        'width'        => '15px',
        'display'      => 'grid',
        'place-content'=> 'center',
        'transition'   => 'transform 0.2s',
      ],

      '.bf-country-fld-wrp.menu-open .bf-dpd-down-btn'=> ['transform'=> 'rotate(180deg)'],

      '.bf-option-list .disabled-opt'=> [
        'pointer-events'=> 'none',
        'cursor'        => 'not-allowed',
        'color'         => 'var(--bg-10) !important',
        'opacity'       => '0.5',
      ],

      '.bf-file-upload-container'=> [
        'position'=> 'relative',
        'height'  => '40px',
        'width'   => '100%',
        'display' => 'inline-block',
      ],

      '.bf-fld-wrp.readonly .bf-file-input-wrpr'=> [
        'opacity'       => '.bf-7',
        'cursor'        => 'not-allowed',
        'pointer-events'=> 'none',
      ],

      '.bf-fld-wrp.disabled .bf-file-input-wrpr'=> [
        'opacity'       => '.bf-5',
        'cursor'        => 'not-allowed',
        'pointer-events'=> 'none',
      ],

      '.bf-btn-wrpr'=> [
        'display'    => 'flex',
        'align-items'=> 'center',
        'position'   => 'relative',
        'gap'        => '5px',
      ],

      '.bf-inp-btn'=> [
        'align-items'     => 'center',
        'background-color'=> 'var(--btn-bg) !important',
        'border'          => 'none',
        'border-radius'   => 'var(--g-bdr-rad) !important',
        'box-shadow'      => 'none',
        'color'           => 'hsla(0, 0%, 100%, 100%)',
        'cursor'          => 'pointer',
        'display'         => 'inline-flex',
        'padding'         => 'var(--btn-p) !important',
        'font-size'       => 'var(--btn-fs) !important',
        'gap'             => '5px',
        'line-height'     => '1',
      ],

      '.bf-inp-btn:focus-visible'=> [
        'outline'       => '2px solid var(--global-accent-color)',
        'outline-offset'=> '2px',
        'transition'    => 'outline-offset 0.2s ease',
      ],
      '.bf-inp-btn:active:focus-visible'=> [
        'outline-offset'=> '0',
      ],

      '.bf-file-select-status'=> [
        'font-size'=> '14px',
        'color'    => 'var(--global-font-color)',
      ],

      '.bf-max-size-lbl'=> [
        'color'    => 'hsla(var(--gfh), var(--gfs), var(--gfl), 0.7)',
        'font-size'=> '10px',
      ],

      '.bf-file-upload-input'=> [
        'display' => 'block',
        'width'   => '100%',
        'height'  => '100%',
        'border'  => 'none',
        'position'=> 'absolute',
        'top'     => '0',
        'left'    => '0',
        'opacity' => '0',
        'cursor'  => 'pointer',
      ],

      '.bf-file-input-wrpr .files-list'=> [],

      '.bf-file-input-wrpr .file-wrpr'=> [
        'background-color'=> 'var(--bg-10)',
        'border-radius'   => 'var(--g-bdr-rad)',
        'display'         => 'flex',
        'align-items'     => 'center',
        'justify-content' => 'space-between',
        'width'           => '100%',
        'height'          => 'auto',
        'margin-top'      => '10px',
        'padding'         => '5px',
        'outline'         => '1px solid var(--bg-15)',
      ],

      '.bf-file-input-wrpr .file-preview'=> [
        'border-radius'=> 'calc(var(--g-bdr-rad) - 4px)',
        'height'       => '25px',
        'width'        => '25px',
        'outline'      => '1px solid var(--bg-5)',
      ],

      '.bf-file-input-wrpr .file-details'=> [
        'display'        => 'flex',
        'align-items'    => 'center',
        'justify-content'=> 'space-between',
        'padding'        => '0px 10px',
        'width'          => '94%',
      ],

      '.bf-file-input-wrpr .file-title'=> [
        'display'      => 'inline-block',
        'font-size'    => '12px',
        'overflow'     => 'hidden',
        'text-overflow'=> 'ellipsis',
        'white-space'  => 'nowrap',
        'color'        => 'var(--global-font-color)',
      ],

      '.bf-file-input-wrpr .file-size'=> [
        'font-size'  => '12px',
        'line-height'=> '1',
        'color'      => 'hsla(var(--gfh), var(--gfs), var(--gfl), 0.7)',
      ],

      '.bf-file-input-wrpr .cross-btn'=> [
        'cursor'          => 'pointer',
        'border'          => 'none',
        'border-radius'   => '50px',
        'box-shadow'      => 'none',
        'color'           => 'hsla(var(--gfh), var(--gfs), var(--gfl), 0.8)',
        'font-size'       => '20px',
        'height'          => '25px',
        'line-height'     => '1',
        'min-height'      => '25px',
        'min-width'       => '25px',
        'padding'         => '0',
        'text-decoration' => 'none',
        'width'           => '25px',
        'transition'      => 'background-color 150ms',
        'background-color'=> 'var(--bg-15)',
      ],
      '.bf-file-input-wrpr .cross-btn:hover'=> [
        'background-color'=> 'var(--bg-20)',
        'color'           => 'var(--global-font-color)',
      ],
      '.bf-file-input-wrpr .bf-err-wrp'=> [
        'display'         => 'none',
        'opacity'         => '0',
        'transition'      => 'display 1s, opacity 1s',
        'align-items'     => 'center',
        'background-color'=> 'hsla(0, 100%, 97%, 100%)',
        'color'           => 'darkred',
        'border-radius'   => '10px',
        'height'          => '40px',
        'margin-top'      => '10px',
        'padding'         => '2px 10px',
        'width'           => '100%',
      ],

      '.bf-file-input-wrpr .bf-err-wrp.active'=> [
        'display'   => 'flex',
        'opacity'   => '1',
        'transition'=> 'display 1s, opacity 1s',
      ],

      '.bf-dpd-fld-container'=> [
        'position'=> 'relative',
        'height'  => '40px',
        'width'   => '100%',
        'display' => 'inline-block',
      ],

      '.bf-dpd-fld-wrp'=> [
        'position'        => 'absolute',
        'width'           => '100%',
        'background-color'=> 'var(--global-fld-bg-color)',
        'border-style'    => 'var(--global-fld-bdr) !important',
        'border-color'    => 'var(--global-fld-bdr-clr) !important',
        'border-radius'   => 'var(--g-bdr-rad) !important',
        'border-width'    => 'var(--g-bdr-width) !important',
        'font-size'       => 'var(--fld-fs) !important',

        'color'         => 'var(--fld-inp-c) !important',
        'overflow'      => 'hidden',
        'display'       => 'flex',
        'flex-direction'=> 'column',
        'transition'    => 'box-shadow 200ms',
      ],

      '.bf-dpd-fld-wrp:hover:not(.menu-open):not(.disabled)'=> [
        'border-color'=> 'var(--global-accent-color)!important',
      ],

      '.bf-dpd-fld-wrp:focus-within:not(.menu-open):not(.disabled)'=> [
        'box-shadow'  => '0 0 0 3px hsla(var(--gah), var(--gas), var(--gal), 0.30) !important',
        'border-color'=> 'var(--global-accent-color)!important',
      ],

      '.bf-dpd-fld-wrp.disabled .bf-dpd-wrp'=> [
        'cursor'          => 'not-allowed',
        'pointer-events'  => 'none',
        'background-color'=> 'hsla(var(--gfbg-h), var(--gfbg-s), calc(var(--gfbg-l) + 20%), var(--gfbg-a))',
        'color'           => 'hsla(var(--gfh), var(--gfs), calc(var(--gfl) + 40%), var(--gfa))',
        'border-color'    => 'hsla(var(--gfbc-h), var(--gfbc-s), calc(var(--gfbc-l) + 20%), var(--gfbc-a))',
      ],
      '.bf-dpd-fld-wrp.readonly .bf-dpd-wrp'=> [
        'pointer-events'  => 'none',
        'background-color'=> 'hsla(var(--gfbg-h), var(--gfbg-s), calc(var(--gfbg-l) + 20%), var(--gfbg-a))',
        'color'           => 'hsla(var(--gfh), var(--gfs), calc(var(--gfl) + 40%), var(--gfa))',
        'border-color'    => 'hsla(var(--gfbc-h), var(--gfbc-s), calc(var(--gfbc-l) + 20%), var(--gfbc-a))',
      ],

      '.bf-dpd-wrp'=> [
        'background-color'=> 'transparent',
        'overflow'        => 'hidden',
        'font-weight'     => '500',
        'display'         => 'flex',
        'justify-content' => 'space-between',
        'align-items'     => 'center',
        'cursor'          => 'pointer',
        'padding'         => '4px 10px',
        'box-sizing'      => 'border-box',
        'min-height'      => '38px',

        'position'=> 'relative',
        'outline' => 'none',
        /* border      => 1px solid 'red', */
      ],
      '.bf-dpd-fld-wrp.menu-open'=> [
        'background-color'=> 'var(--bg-0)',
        'z-index'         => '999',
        'box-shadow'      => '0px 1.2px 2.2px hsla(0, 0%, 0%, 3.2%),
          0px 2.9px 5.3px hsla(0, 0%, 0%, 4.5%),
          0px 5.4px 10px hsla(0, 0%, 0%, 5.4%),
          0px 9.6px 17.9px hsla(0, 0%, 0%, 6.2%),
          0px 18px 33.4px hsla(0, 0%, 0%, 7.3%),
          0px 43px 80px hsla(0, 0%, 0%, 10%)',
        'border-color'=> 'var(--bg-10)!important',
      ],

      '.bf-selected-opt-wrp'=> [
        'height'     => '100%',
        'display'    => 'flex',
        'align-items'=> 'center',
        'gap'        => '10px',
      ],

      '.bf-selected-opt-lbl.multi-chip'=> [
        'display'  => 'flex',
        'flex-wrap'=> 'wrap',
        'gap'      => '5px',
      ],

      '.bf-selected-opt-lbl .chip-wrp'=> [
        'display'         => 'flex',
        'align-items'     => 'center',
        'background-color'=> 'var(--bg-10)',
        'padding'         => '5px 8px',
        'border-radius'   => 'var(--g-bdr-rad)',
        'gap'             => '5px',
      ],

      '.bf-selected-opt-lbl .chip-icn'=> [
        'width' => '13px',
        'height'=> '13px',
      ],

      '.bf-selected-opt-lbl .chip-lbl'=> [
        'font-size'  => 'var(--fld-fs) !important',
        'font-weight'=> 'var(--fld-f-w) !important',
        'font-style' => 'var(--fld-f-style) !important',
        'color'      => 'var(--fld-inp-c)',
      ],

      '.bf-selected-opt-lbl .chip-clear-btn'=> [
        'border'          => 'none',
        'outline'         => 'none',
        'box-shadow'      => 'none',
        'border-radius'   => '50%',
        'cursor'          => 'pointer',
        'display'         => 'grid',
        'place-content'   => 'center',
        'height'          => '17px',
        'width'           => '17px',
        'background-color'=> 'var(--bg-20) !important',
        'color'           => 'var(--fld-inp-c) !important',
        'padding'         => '0px',
      ],

      '.bf-selected-opt-lbl .chip-clear-btn:hover'=> [
        'background-color'=> 'var(--bg-15) !important',
      ],

      '.bf-selected-opt-lbl .chip-clear-btn:focus-visible'=> [
        'outline'=> '1.5px solid var(--global-accent-color)',
      ],

      '.bf-selected-opt-lbl'=> [

      ],

      '.bf-selected-opt-img'=> [
        'height'       => '17px',
        'width'        => '25px',
        'border-radius'=> '3px',

        '-webkit-user-select'=> 'none',
        'user-select'        => 'none',
      ],

      '.bf-dpd-fld-wrp .placeholder-img'=> [
        'background-color'=> 'var(--bg-15)',
        'outline'         => '1px solid var(--bg-10)',
      ],

      '.bf-selected-opt-clear-btn'=> [
        'display'      => 'none',
        'right'        => '6px',
        'padding'      => '0px !important',
        'margin'       => '0',
        'background'   => 'transparent !important',
        'border'       => '0',
        'outline'      => '0',
        'cursor'       => 'pointer',
        'margin-right' => '5px',
        'place-content'=> 'center',
        'width'        => '16px',
        'height'       => '16px',
        'border-radius'=> '50% !important',
        'color'        => 'var(--fld-inp-c) !important',
      ],

      '.bf-selected-opt-clear-btn:hover'=> [
        'background-color'=> 'var(--bg-15) !important',
      ],

      '.bf-selected-opt-clear-btn:focus-visible'=> [
        'outline'=> '1.5px solid var(--global-accent-color)',
      ],

      '.bf-dpd-btn-wrp'=> [
        'display'    => 'flex',
        'align-items'=> 'center',
      ],

      '.bf-option-wrp'=> [
        'max-height'    => '0px',
        'min-height'    => 'auto',
        'margin'        => 'auto',
        'width'         => '100%',
        'overflow'      => 'hidden',
        'display'       => 'flex',
        'flex-direction'=> 'column',
      ],

      '.bf-option-inner-wrp'=> [
        'overflow'      => 'hidden',
        'display'       => 'flex',
        'flex-direction'=> 'column',
      ],

      '.bf-option-list'=> [
        'padding'   => '0',
        'margin'    => '0 0 2px 0 !important',
        'height'    => '100%',
        'overflow-y'=> 'auto',

        /* firefox */
        'scrollbar-width'=> 'thin !important',
        'scrollbar-color'=> 'var(--bg-15) transparent !important',
      ],

      '.bf-option-list::-webkit-scrollbar'=> [
        'width'=> '8px',
      ],

      /* .option-list::-webkit-scrollbar-track'=> [
          'background'=> #'fafafa',
        ], */

      '.bf-option-list::-webkit-scrollbar-thumb'=> [
        'background-color'=> 'var(--bg-15)',
        'border-radius'   => 'var(--g-bdr-rad)',
      ],

      '.bf-option-list:not(.active-list)'=> [
        'display'=> 'none !important',
      ],

      '.bf-option-search-wrp'=> [
        'position'=> 'relative',

        'margin'=> '5px 5px 0 5px',
      ],

      '.bf-icn'=> [
        'position' => 'absolute',
        'top'      => '50%',
        'transform'=> 'translateY(-50%)',
      ],

      '.bf-opt-search-icn'=> [
        'left'=> '11px',
      ],

      '.bf-opt-search-input'=> [
        'width'     => '100%',
        'padding'   => '0px 35px',
        'outline'   => 'none',
        'box-shadow'=> 'none',
        'border'    => 'none !important',
        /* border-'top'=> 1px solid #'ddd', */
        /* border-bottom=> 1px solid #'ddd', */
        'height'          => '35px',
        'border-radius'   => 'calc(var(--g-bdr-rad) - 1px) !important',
        'background-color'=> 'var(--bg-5)',
        'font-size'       => 'var(--fld-fs)',
        'font-family'     => 'inherit',
        'color'           => 'var(--global-font-color) !important',
      ],
      '.bf-opt-search-input::placeholder'=> [
        'color'=> 'hsla(var(--gfh), var(--gfs), var(--gfl), 0.5)',
      ],
      '.bf-opt-search-input:focus'=> [
        'background-color'=> 'var(--bg-0)',
        'box-shadow'      => '0 0 0 2px var(--global-accent-color) inset',
      ],
      '.bf-opt-search-input:focus~svg'=> ['color'=> 'var(--global-accent-color)'],

      '.bf-opt-search-input::-webkit-search-decoration'        => ['display'=> 'none'],
      '.bf-opt-search-input::-webkit-search-cancel-button'     => ['display'=> 'none'],
      '.bf-opt-search-input::-webkit-search-results-button'    => ['display'=> 'none'],
      '.bf-opt-search-input::-webkit-search-results-decoration'=> ['display'=> 'none'],

      '.bf-search-clear-btn'=> [
        'display'      => 'none',
        'right'        => '8px',
        'padding'      => '0px !important',
        'margin'       => '0',
        'background'   => 'var(--bg-25) !important',
        'border'       => '0',
        'outline'      => '0',
        'cursor'       => 'pointer',
        'place-content'=> 'center',
        'width'        => '16px',
        'height'       => '16px',
        'border-radius'=> '50% !important',
        'color'        => 'var(--bg-0)',
      ],

      '.bf-search-clear-btn:hover'=> [
        'background'=> 'var(--bg-50) !important',
      ],

      '.bf-search-clear-btn:focus-visible'=> [
        'box-shadow'=> '0 0 0 2px var(--global-accent-color)',
        'outline'   => 'none',
      ],

      '.bf-custom-opt-btn'=> [
        'display'      => 'none',
        'right'        => '30px',
        'padding'      => '5px',
        'margin'       => '0',
        'background'   => 'transparent',
        'border'       => '0.5px solid hsla(var(--gfbg-h), var(--gfbg-s), 70%, var(--gfbg-a)) ',
        'outline'      => '0',
        'cursor'       => 'pointer',
        'place-content'=> 'center',
        'height'       => '25px',
        'border-radius'=> '5px',
        'color'        => 'var(--global-font-color)',
      ],

      '.bf-option-list .option'=> [
        'margin'         => '2px 5px !important',
        'transition'     => 'background 0.2s',
        'border-radius'  => 'calc(var(--g-bdr-rad) - 2px)',
        'font-size'      => 'calc(var(--fld-fs) - 2px)',
        'cursor'         => 'pointer',
        'text-align'     => 'left',
        'padding'        => '8px 7px',
        'display'        => 'flex',
        'align-items'    => 'center',
        'justify-content'=> 'space-between',
      ],

      '.bf-option-list .option:hover:not(.bf-selected-opt):not(.opt-group-title)'=> [
        'background-color'=> 'var(--bg-10)',
      ],

      '.bf-option-list .option:focus-visible'=> [
        'outline'         => '2px solid var(--global-accent-color)',
        'background-color'=> 'var(--bg-10)',
      ],

      '.bf-option-list .selected-opt:focus-visible'=> [
        'background-color'=> 'var(--global-accent-color)',
      ],

      '.bf-option-list .opt-group-title'=> [
        'font-size'  => 'calc(var(--fld-fs) - 2px)',
        'cursor'     => 'default',
        'opacity'    => '.bf-7',
        'font-weight'=> 600,
      ],

      '.bf-option-list .opt-group-child'=> [
        'padding-left'=> '15px !important',
      ],

      '.bf-option-list .selected-opt'=> [
        'font-weight'     => 500,
        'background-color'=> 'var(--global-accent-color)',
      ],

      '.bf-option-list .opt-not-found'=> [
        'text-align'=> 'center',
        'list-style'=> 'none',
        'margin'    => '5px',
      ],

      '.bf-option-list .opt-lbl-wrp'=> [
        'display'    => 'flex',
        'align-items'=> 'center',
        'gap'        => '5px',
      ],
      '.bf-option-list .opt-lbl'=> [],

      '.bf-option-list .opt-icn'=> [
        'margin-right'       => '5px',
        'height'             => '17px',
        'width'              => '25px',
        'border-radius'      => '3px',
        '-webkit-user-select'=> 'none',
        'user-select'        => 'none',
      ],

      '.bf-dpd-down-btn'=> [
        'width'        => '15px',
        'display'      => 'grid',
        'place-content'=> 'center',
        'transition'   => 'transform 0.2s',
      ],

      '.bf-dpd-fld-wrp.menu-open .bf-dpd-down-btn'=> ['transform'=> 'rotate(180deg)'],

      '.bf-option-list .disabled-opt'=> [
        'pointer-events'=> 'none',
        'cursor'        => 'not-allowed',
        'color'         => 'var(--bg-10) !important',
        'opacity'       => '0.5',
      ],

      '.bf-option-list .disable-on-max'=> [
        'pointer-events'=> 'none',
        'cursor'        => 'not-allowed',
        'color'         => 'var(--bg-10) !important',
        'opacity'       => '0.8',
      ],

      '.bf-paypal-wrp'=> [
        'width'    => '100%',
        'min-width'=> '150px',
        'max-width'=> '750px',
        'margin'   => 'auto',
      ],

      '.bf-razorpay-wrp'=> [
        'display'        => 'flex',
        'justify-content'=> 'center',
      ],

      '.bf-razorpay-btn'=> [
        'cursor'          => 'pointer',
        'position'        => 'relative',
        'display'         => 'inline-block',
        ' min-width'      => '160px',
        'height'          => '45px',
        'border-radius'   => ' 3px',
        'text-align'      => 'center',
        'font-style'      => 'italic',
        'overflow'        => 'hidden',
        'background-color'=> 'hsla(216, 85%, 18%, 100%) !important',
        'border'          => 'solid hsla(216, 85%, 18%, 100%)',
        'border-width'    => '1px',
        'padding'         => '0px !important',

        'box-shadow'=> 'hsla(0, 0%, 0%, 0.1) 0px 4px 12px',
      ],

      '.bf-razorpay-btn::before'=> [
        'content'         => '""',
        'position'        => 'absolute',
        'left'            => '-1em',
        'top'             => 0,
        'width'           => '46px',
        'height'          => '100%',
        'background-color'=> 'hsla(224, 68%, 37%, 100%)',
        'border-radius'   => '2px 0 0 2px',
        'transform'       => 'skewX(-15deg)',
      ],

      '.bf-razorpay-btn svg'=> [
        'position' => 'absolute',
        'left'     => 0,
        'top'      => '50%',
        'margin'   => '0px 7px',
        'transform'=> 'translateY(-50%)',
        'width'    => '23px',
        'height'   => '25px',
      ],

      '.bf-razorpay-btn-text'=> [
        'padding'       => '4px 28px 4px 60px',
        'margin'        => '1px 0px',
        'color'         => 'hsla(0, 0%, 100%, 100%)',
        'display'       => 'flex',
        'flex-direction'=> 'column',
      ],

      '.bf-razorpay-btn-title'=> [
        'display'    => 'block',
        'min-height' => '18px',
        'line-height'=> '18px',
        'font-size'  => '16px',
        'font-weight'=> '800',
        'opacity'    => '0.95',
      ],

      '.bf-razorpay-btn-sub-title'=> [
        'opacity'  => '0.6',
        'font-size'=> '8px',
      ],

      '.bf-err-wrp'=> [
        'transition'        => 'all .3s',
        'display'           => 'grid',
        'grid-template-rows'=> '0fr',
      ],
      '.bf-err-inner'=> [
        'overflow'=> 'hidden',
      ],
      '.bf-err-msg'=> [
        'background-color'=> 'var(--err-bg, none)',
        'color'           => 'var(--err-c, inherit)',
        'font-size'       => 'var(--err-txt-fs)',
        'display'         => 'flex',
        'align-items'     => 'center',
        'text-align'      => 'var(--err-txt-al, init)',
        'padding'         => 'var(--err-p, 0)',
        'margin'          => 'var(--err-m, 0)',
        'box-shadow'      => 'var(--err-sh, none)',
        'border-style'    => 'var(--err-bdr, medium)',
        'border-color'    => 'var(--err-bdr-clr, none)',
        'border-radius'   => 'var(--err-bdr-rad, 0)',
        'border-width'    => 'var(--err-bdr-width, 0)',
        'font-weight'     => 'var(--err-txt-font-w)',
        'font-style'      => 'var(--err-txt-font-style)',
        'height'          => 'var(--err-h)',
      ],
      '.bf-err-txt'=> [
        'display'=> 'block',
      ],
      '.bit-form .d-none'=> [
        'display'=> 'none !important'
      ],
      '.bit-form .v-hide'=> [
        'visibility'=> 'hidden !important'
      ],
      '.bf-form-msg'=> [
        'background'   => '#ffe8c3',
        'border-radius'=> '6px',
        'margin'       => '6px 0px',
        'padding'      => '5px 14px',
        'color'        => '#776f63',
        'height'       => '0',
        'opacity'      => '0',
        'overflow'     => 'hidden',
        'transition'   => 'all 0.5s ease-out',
      ],
      '.bf-form-msg.success'=> [
        'background'=> '#c5f7dd',
      ],
      '.bf-form-msg.error'=> [
        'background'=> '#ffd0cb',
      ],
      '.bf-form-msg.warning'=> [
        'background'=> '#ffe8c3',
      ],
      '.bf-form-msg.active'=> [
        'height'    => 'auto',
        'opacity'   => '1',
        'transition'=> 'all 1s ease-out',
      ],
      '.btcd-fld-itm'=> [
        'transition'=> 'all 0.2s ease',
      ],
      '.fld-hide'=> [
        'min-height'=> '0px !important',
        'height'    => '0px !important',
        'overflow'  => 'hidden !important',
      ],
      '@keyframes bf-rotation'=> [
        '0%'=> [
          'transform'=> 'rotate(0deg)',
        ],
        '100%'=> [
          'transform'=> 'rotate(360deg)',
        ],
      ],
      '.bf-spinner'=> [
        'margin-left'  => '5px',
        'width'        => '15px',
        'height'       => '15px',
        'border-radius'=> '50%',
        'border-top'   => '2px solid',
        'border-right' => '2px solid transparent',
        'animation'    => 'bf-rotation 1s linear infinite',
      ],
      '.bit-form *'=> [
        'box-sizing' => 'border-box !important',
        'font-family'=> 'var(--g-font-family)',
      ],
      '.bit-form'=> [
        'background'=> 'var(--global-bg-color)',
      ],
      '.bit-form form'=> [
        'direction'=> 'var(--dir)',
      ],
      '.bit-form p'=> [
        'margin'=> '0',
      ],
    ];
  }
}
