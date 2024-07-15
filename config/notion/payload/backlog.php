<?php

return [
    "filter" => [
        "and" => [
            [
                "property" => "DeadLine Date",
                "relation" => [
                    "contains" => "%parentPageId%"
                ]
            ],
            [
                "or" => [
                    [
                        "property" => "Status",
                        "select" => [
                            "equals" => "In Review"
                        ]
                    ],
                    [
                        "property" => "Status",
                        "select" => [
                            "equals" => "Planned"
                        ]
                    ],
                    [
                        "property" => "Status",
                        "select" => [
                            "equals" => "Released"
                        ]
                    ],
                    [
                        "property" => "Status",
                        "select" => [
                            "equals" => "Merged"
                        ]
                    ],
                    [
                        "property" => "Status",
                        "select" => [
                            "equals" => "In Progress"
                        ]
                    ],
                    [
                        "property" => "Status",
                        "select" => [
                            "equals" => "Planning"
                        ]
                    ]
                ]
            ]
            // [
            //     "or" => [
            //         [
            //             "property" => "Manager",
            //             "people" => [
            //                 "contains" => ""
            //             ]
            //         ]
            //     ]
            // ]
        ]
    ]
];
