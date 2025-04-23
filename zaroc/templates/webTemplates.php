<?php
function webStructureTemplate(array $session, array $structure, array $components)
{
    $intentName = getIntent();

    dfOpen();
    structureOpen($intentName);
    openPayload();
    openWeb();

    for ($i = 0; $i < count($structure); $i++):
        switch ($structure[$i]) {
            case 'card':
                webCard(
                    $components[$i][0],
                    $components[$i][1],
                    $components[$i][2]
                );
                break;

            case 'button':
                webButton(
                    $components[$i][0],
                    $components[$i][1],
                    $components[$i][2],
                    $components[$i][3]
                );
                break;

            case 'reply':
                webReply(
                    $components[$i][0],
                    $components[$i][1],
                    $components[$i][2]
                );
                break;

            case 'description':
                webDescription(
                    $components[$i][0],
                    $components[$i][1]
                );
                break;

            case 'image':
                webImage(
                    $components[$i][0],
                    $components[$i][1]
                );
                break;

            case 'list':
                webList(
                    $components[$i][0],
                    $components[$i][1],
                    $components[$i][2],
                    $components[$i][3]
                );
                break;

            case 'paragraph':
                webParagraph(
                    $components[$i][0],
                    $components[$i][1]
                );
                break;

            case 'comma':
                comma();
                break;

            case 'basicDivider':
                basicDivider();
                break;

            case 'commaDivider':
                commaDivider();
                break;

            case 'superDivider':
                superDivider();
                break;
        }
    endfor;

    closeWeb();
    closePayload();
    structureClose();

    // Solo si session[0] existe y no está vacío o nulo
    if (isset($session[0]) && $session[0] !== '' && $session[0] !== null) {
        comma();
        contextOpen();
        echo $session[0];
        contextClose();
    }

    dfClose();
}
