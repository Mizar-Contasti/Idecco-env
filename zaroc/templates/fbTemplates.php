<?php

function fbStructureTemplate(array $session, array $structure, array $components)
{
    $intentName = getIntent();

    dfOpen();
      structureOpen($intentName);

      for ($i = 0; $i < count($structure); $i++):
        switch ($structure[$i]) {
          case 'comma':
            comma();
            break;
          case 'image':
            fbImage($components[$i][0]);
            break;
          case 'paragraph':
            fbParagraph($components[$i][0]);
            break;
          case 'reply':
            fbReply($components[$i][0], $components[$i][1]);
            break;
          case 'card':
            fbCard(
              $components[$i][0],
              $components[$i][1],
              $components[$i][2],
              $components[$i][3],
              $components[$i][4]
            );
            break;
          case 'openPayload':
            openPayload();
            break;
          case 'generic':
            fbExternalGeneric(
              $components[$i][0],
              $components[$i][1],
              $components[$i][2],
              $components[$i][3],
              $components[$i][4],
              $components[$i][5],
              $components[$i][6]
            );
            break;
          case 'media':
            fbExternalMedia(
              $components[$i][0],
              $components[$i][1],
              $components[$i][2],
              $components[$i][3]
            );
            break;
          case 'receipt':
            fbExternalReceipt(
              $components[$i][0],
              $components[$i][1],
              $components[$i][2],
              $components[$i][3],
              $components[$i][4],
              $components[$i][5],
              $components[$i][6],
              $components[$i][7],
              $components[$i][8],
              $components[$i][9],
              $components[$i][10]
            );
            break;
          case 'closePayload':
            closePayload();
            break;
        }
      endfor;

      structureClose();

      // Sólo si existe session[0] y tiene contenido (no es cadena vacía ni null)
      if (isset($session[0]) && $session[0] !== '' && $session[0] !== null) {
        comma();
        contextOpen();
        echo $session[0];
        contextClose();
      }

    dfClose();
}
