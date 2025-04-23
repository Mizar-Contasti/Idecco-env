<?php

function wsStructureTemplate(array $session, array $structure, array $components)
{
    $intentName = getIntent();

    dfOpen();
    dfCore($intentName);
    comma();
    openWhatsapp();

    for ($i = 0; $i < count($structure); $i++):
        if ($structure[$i] === 'comma') {
            comma();
        }
        if ($structure[$i] === 'paragraph') {
            wsParagraph($components[$i][0]);
        }
        if ($structure[$i] === 'button') {
            wsButton(
                $components[$i][0],
                $components[$i][1],
                $components[$i][2],
                $components[$i][3],
                $components[$i][4],
                $components[$i][5]
            );
        }
        if ($structure[$i] === 'list') {
            wsList(
                $components[$i][0],
                $components[$i][1],
                $components[$i][2],
                $components[$i][3],
                $components[$i][4],
                $components[$i][5],
                $components[$i][6],
                $components[$i][7],
                $components[$i][8]
            );
        }
    endfor;

    closeWhatsapp();

    // Solo si el primer elemento de $session existe y no es cadena vacía ni nulo
    if (isset($session[0]) && $session[0] !== '' && $session[0] !== null) {
        comma();
        contextOpen();
        echo $session[0];
        contextClose();
    }

    dfClose();
}
