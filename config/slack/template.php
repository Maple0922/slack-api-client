<?php

return [
    'developPoint' => [
        'main' => [
            'username' => '開発ポイント',
            'icon_emoji' => ':chipichapa:',
            'text' => '開発ポイント達成進捗',
            "blocks" => [
                [
                    "type" => "section",
                    "text" => [
                        "type" => "mrkdwn",
                        "text" => "<!subteam^S033HEA941W|@engineer_executive> %eol%%eol% <https://www.notion.so/wizleap/7607c2507a5d49ec905083be14f65055?v=9d9923cd217d4876b615e39933f27725|Backlog>を更新してください %eol%%eol% :chipichapa: *今週タスク達成率: %doneRate% (%donePoint%/%totalPoint%)* :chipichapa: %eol%%eol% :chipichapa: *目標ポイント達成率: %targetRate% (%donePoint%/%targetPoint%)* :chipichapa:"
                    ],
                ],
                [
                    "type" => "divider"
                ]
            ]
        ],

        'member' => [
            [
                "type" => "section",
                "text" => [
                    "type" => "mrkdwn",
                    "text" => ":bust_in_silhouette: *%memberName%*%eol%%eol%%doneRateStars%"
                ],
                "accessory" => [
                    "type" => "image",
                    "image_url" => "%imageUrl%",
                    "alt_text" => "Alt"
                ],
                'fields' => [
                    [
                        'type' => 'mrkdwn',
                        'text' => '*今週タスク達成率*%eol%_%doneRate% (%donePoint%/%totalPoint%)_'
                    ],
                    [
                        'type' => 'mrkdwn',
                        'text' => '*目標ポイント達成率*%eol%_%targetRate% (%donePoint%/%targetPoint%)_'
                    ],
                ]
            ],
            [
                "type" => "divider"
            ],
        ],

        'failed' => [
            'username' => '開発ポイント',
            'icon_emoji' => ':chipichapa:',
            'blocks' => [
                [
                    "type" => "section",
                    "text" => [
                        "type" => "mrkdwn",
                        "text" => "開発ポイントのSlack通知に失敗しました"
                    ],
                ]
            ]
        ]
    ],

    'engineerMtgOrder' => [
        'main' => [
            'username' => 'エンジニア週次MTGの順番を勝手に決める猫',
            'icon_emoji' => ':chipichapa:',
            'text' => 'エンジニア週次MTGの順番を勝手に決める猫',
            "blocks" => [
                [
                    "type" => "section",
                    "text" => [
                        "type" => "mrkdwn",
                        "text" => "<!subteam^S013CMPVD7H>%eol%%members%"
                    ],
                ]
            ]
        ],

        'failed' => [
            'username' => 'エンジニア週次MTGの順番を勝手に決める猫',
            'icon_emoji' => ':chipichapa:',
            'blocks' => [
                [
                    "type" => "section",
                    "text" => [
                        "type" => "mrkdwn",
                        "text" => "エンジニア週次MTGの順番のSlack通知に失敗しました"
                    ],
                ]
            ]
        ]
    ]
];
