<?php

function tgStructureTemplate(array $session, array $structure, array $components)
{
    $intentName = getIntent();

    dfOpen();
    structureOpen($intentName);

    for ($i = 0; $i < count($structure); $i++):
        if ($structure[$i] === 'comma') {
            comma();
        }
        if ($structure[$i] === 'image') {
            tgImage($components[$i][0]);
        }
        if ($structure[$i] === 'paragraph') {
            tgParagraph($components[$i][0]);
        }
        if ($structure[$i] === 'reply') {
            tgReply($components[$i][0], $components[$i][1]);
        }
        if ($structure[$i] === 'card') {
            tgCard(
                $components[$i][0],
                $components[$i][1],
                $components[$i][2],
                $components[$i][3],
                $components[$i][4]
            );
        }
        if ($structure[$i] === 'openPayload') {
            openPayload();
        }
        if ($structure[$i] === 'verticalButtons') {
            tgVerticalButtons(
                $components[$i][0],
                $components[$i][1],
                $components[$i][2]
            );
        }
        if ($structure[$i] === 'requiringNumber') {
            externalRequiringNumber(
                $components[$i][0],
                $components[$i][1]
            );
        }
        if ($structure[$i] === 'closePayload') {
            closePayload();
        }
        if ($structure[$i] === 'test') {
            tgTest();
        }
    endfor;

    structureClose();

    // Sólo si session[0] existe y no está vacío ni nulo
    if (isset($session[0]) && $session[0] !== '' && $session[0] !== null) {
        comma();
        contextOpen();
        echo $session[0];
        contextClose();
    }

    dfClose();
}
