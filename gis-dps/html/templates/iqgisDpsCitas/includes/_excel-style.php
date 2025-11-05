<?php
    //Estilos para todo el libro de excel
    $spreadsheet->getDefaultStyle()->getfont()->setName("Calibri");
    $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
    //Estilos para todo el libro de excel
    $styleArrayTitulos = [
        'font' => [
            'bold' => true,
            'size' =>'8',
            'name' =>'Arial',
            'color' => array('rgb' => 'FFFFFF')
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'wrap'=>true,
        ],
        'borders' => [
            'top' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => [
                'argb' => str_replace('#', '', COLOR_PRINCIPAL),
            ]
        ],
    ];

    $styleArrayTitulos_turno = [
        'font' => [
            'bold' => true,
            'size' =>'8',
            'name' =>'Arial',
            'color' => array('rgb' => 'FFFFFF')
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'wrap'=>true,
        ],
        'borders' => [
            'top' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => [
                'argb' => '1E8449',
            ]
        ],
    ];

    $styleArrayTitulos_almuerzo = [
        'font' => [
            'bold' => true,
            'size' =>'8',
            'name' =>'Arial',
            'color' => array('rgb' => 'FFFFFF')
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'wrap'=>true,
        ],
        'borders' => [
            'top' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => [
                'argb' => '2874A6',
            ]
        ],
    ];

    $styleArrayTitulos_break = [
        'font' => [
            'bold' => true,
            'size' =>'8',
            'name' =>'Arial',
            'color' => array('rgb' => 'FFFFFF')
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'wrap'=>true,
        ],
        'borders' => [
            'top' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => [
                'argb' => 'F1C40F',
            ]
        ],
    ];

    $styleArrayTitulos_pausa = [
        'font' => [
            'bold' => true,
            'size' =>'8',
            'name' =>'Arial',
            'color' => array('rgb' => 'FFFFFF')
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'wrap'=>true,
        ],
        'borders' => [
            'top' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => [
                'argb' => 'B03A2E',
            ]
        ],
    ];

    $styleArrayTitulos_capacitacion = [
        'font' => [
            'bold' => true,
            'size' =>'8',
            'name' =>'Arial',
            'color' => array('rgb' => 'FFFFFF')
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'wrap'=>true,
        ],
        'borders' => [
            'top' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => [
                'argb' => '6C3483',
            ]
        ],
    ];

    $styleArrayTitulos_retro = [
        'font' => [
            'bold' => true,
            'size' =>'8',
            'name' =>'Arial',
            'color' => array('rgb' => 'FFFFFF')
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'wrap'=>true,
        ],
        'borders' => [
            'top' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => [
                'argb' => '1ABC9C',
            ]
        ],
    ];

    $styleArrayContenido = [
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'wrap'=>true,
        ],
    ];
?>