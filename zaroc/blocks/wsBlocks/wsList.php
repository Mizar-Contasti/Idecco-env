<?php

function wsList(
    array $wsHeaderType,
    array $wsHeaderContent,
    array $wsBodyContent,
    array $wsFooterContent,
    array $wsButtonTitle,
    array $sectionTitle,
    array $rowId,
    array $rowTitle,
    array $rowDescription
) {

    if ($wsHeaderType[0] == "image") {
        $header = [
            'type' => 'image',
            'image' => [
                'link' => $wsHeaderContent[0],
            ],
        ];
    } else if ($wsHeaderType[0] == "text") {
        $header = [
            'type' => 'text',
            'text' => $wsHeaderContent[0],
        ];
    } else if ($wsHeaderType[0] == "document") {
        $header = [
            'type' => 'document',
            'document' => [
                'link' => $wsHeaderContent[0],
                'filename' => 'file.pdf',
            ]
        ];
    } else if ($wsHeaderType[0] == "video") {

        $header = [
            'type' => 'video',
            'video' => [
                'link' => $wsHeaderContent[0],
            ]
        ];


    } else {
        $header = [
            'type' => 'text',
            'text' => 'No Header type defined at wsButton',
        ];
    }



    $data = [
        'type' => 'interactive',

        'interactive' => [
            'type' => 'list',
            'header' => $header,
            'body' => [
                'text' => $wsBodyContent[0],
            ],
            'footer' => [
                'text' => $wsFooterContent[0],
            ],
            'action' => [
                'button' => $wsButtonTitle[0],
                'sections' => [],
            ],
        ],
    ];

    for ($i = 0; $i < count($sectionTitle); $i++) {
        $section = [
            'title' => $sectionTitle[$i],
            'rows' => [],
        ];

        for ($j = 0; $j < count($rowId[$i]); $j++) {
            $row = [
                'id' => $rowId[$i][$j],
                'title' => $rowTitle[$i][$j],
                'description' => $rowDescription[$i][$j],
            ];

            $section['rows'][] = $row;
        }

        $data['interactive']['action']['sections'][] = $section;
    }

    // return json_encode($data);
    $result = json_encode($data);
    echo ($result);


}

?>