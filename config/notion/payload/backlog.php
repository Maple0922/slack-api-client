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
                        "status" => [
                            "equals" => "In Review"
                        ]
                    ],
                    [
                        "property" => "Status",
                        "status" => [
                            "equals" => "Planned"
                        ]
                    ],
                    [
                        "property" => "Status",
                        "status" => [
                            "equals" => "Released"
                        ]
                    ],
                    [
                        "property" => "Status",
                        "status" => [
                            "equals" => "Merged"
                        ]
                    ],
                    [
                        "property" => "Status",
                        "status" => [
                            "equals" => "In Progress"
                        ]
                    ],
                    [
                        "property" => "Status",
                        "status" => [
                            "equals" => "Planning"
                        ]
                    ]
                ]
            ],
            [
                "property" => "Point",
                "number" => [
                    "greater_than" => 0
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
