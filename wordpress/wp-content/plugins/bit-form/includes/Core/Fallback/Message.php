<?php

namespace BitCode\BitForm\Core\Fallback;

use BitCode\BitForm\Core\Database\SuccessMessageModel;

class Message
{
  public function confirmation()
  {
    $sucMsgMdl = new SuccessMessageModel();
    $messages = $sucMsgMdl->get(
      ['id', 'message_config']
    );

    if (!is_wp_error($messages)) {
      foreach ($messages as $msg) {
        $messageId = $msg->id;
        $messageConfig = new \stdClass();
        $messageConfig->msgType = 'snackbar';
        $messageConfig->position = 'bottom-right';
        $messageConfig->animation = 'slide-up';
        $messageConfig->autoHide = true;
        $messageConfig->duration = 5;

        $styles = [
          'width'           => '300px',
          'padding'         => '5px 35px 5px 20px',
          'background'      => '#fafafa',
          'color'           => '#000000',
          'borderWidth'     => '1px',
          'borderType'      => 'solid',
          'borderColor'     => 'gray',
          'borderRadius'    => '10px',
          'boxShadow'       => [
            [
              'x'      => '0px',
              'y'      => '27px',
              'blur'   => '30px',
              'spread' => '',
              'color'  => "rgb(0 0 0 \/ 18%)",
              'inset'  => '',
            ],
            [
              'x'      => '0px',
              'y'      => '5.2px',
              'blur'   => '9.4px',
              'spread' => '5px',
              'color'  => "rgb(0 0 0 \/ 6%)",
              'inset'  => '',
            ],
            [
              'x'      => '0px',
              'y'      => '11.1px',
              'blur'   => '14px',
              'spread' => '',
              'color'  => "rgb(0 0 0 \/ 14%)",
              'inset'  => '',
            ],
          ],
          'closeBackground' => '#48484829',
          'closeHover'      => '#dfdfdf',
          'closeIconColor'  => '#5a5a5a',
          'closeIconHover'  => '#000',
        ];

        $stylesObj = [
          ".msg-container-$messageId"          => [
            'display'         => 'flex',
            'justify-content' => 'center',
            'align-items'     => 'center',
            'position'        => 'fixed',
            'z-ndex'          => 999,
            'width'           => '300px',
            'height'          => 'auto',
            'visibility'      => 'hidden',
            'opacity'         => 0,
            'transition'      => 'opacity 0.4s',
            'left'            => '50%',
            'margin-eft'      => '-200px',
            'top'             => '30px',
          ],
          ".msg-container-$messageId.active"   => [
            'opacity'    => 1,
            'visibility' => 'visible',
          ],
          ".msg-container-$messageId.deactive" => [
            'opacity'    => 0,
            'visibility' => 'hidden',
            'transition' => 'opacity 0.4s, visibility 0s ease-out 0.4s',
          ],
          ".msg-background-$messageId"         => [
            'width'           => '100%',
            'height'          => '100%',
            'display'         => 'flex',
            'justify-content' => 'center',
            'align-items'     => 'center',
            'background'      => 'rgba(0, 0, 0, 0.0)',
          ],
          ".msg-content-$messageId"            => [
            'background'    => '#fafafa',
            'padding'       => '5px 35px 5px 20px',
            'border-width'  => '1px',
            'border-style'  => 'solid',
            'border-color'  => 'gray',
            'border-radius' => '10px',
            'width'         => '100%',
            'margin'        => 'auto',
            'position'      => 'relative',
            'word-break'    => 'break-all',
            'box-shadow'    => '0px 27px 30px  rgb(0 0 0 / 18%) ,0px 5.2px 9.4px 5px rgb(0 0 0 / 6%) ,0px 11.1px 14px  rgb(0 0 0 / 14%) ',
          ],
          ".close-$messageId"                  => [
            'color'         => '#5a5a5a',
            'background'    => '#48484829',
            'position'      => 'absolute',
            'right'         => '7px',
            'top'           => '50%',
            'transform'     => 'translateY(-50%)',
            'height'        => '25px',
            'width'         => '25px',
            'border'        => 'none',
            'border-radius' => '50%',
            'padding'       => 0,
            'display'       => 'grid',
            'place-content' => 'center',
            'cursor'        => 'pointer',
          ],
          ".close-$messageId:hover"            => [
            'color'         => '#000',
            'background'    => '#dfdfdf',
          ],
          ".close-$messageId:focus"            => [
            'color' => '#000',
          ],
          ".close-icn-$messageId"              => [
            'width'        => '15px',
            'height'       => '15px',
            'stroke-width' => 2,
          ],
        ];

        $messageConfig->styles = $styles;
        $messageConfig->stylesObj = $stylesObj;

        $sucMsgMdl->update(
          [
            'message_config' => wp_json_encode($messageConfig),
          ],
          [
            'id' => $messageId,
          ]
        );
      }
    }
  }
}
