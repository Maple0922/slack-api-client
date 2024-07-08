<?php

return [
    'main' => [
        'username' => '開発ポイント',
        'icon_emoji' => ':chipichapa:',
        "blocks" => [
            [
                "type" => "section",
                "text" => [
                    "type" => "mrkdwn",
                    "text" => "*%topPoint%* / *%bottomPoint%*"
                ],
            ],
            [
                "type" => "divider"
            ],
            // ここにmemberが入る

            // [
            //     "type" => "actions",
            //     "elements" => [
            //         [
            //             "type" => "button",
            //             "text" => [
            //                 "type" => "plain_text",
            //                 "emoji" => true,
            //                 "text" => "Next 2 Results"
            //             ],
            //             "value" => "click_me_123"
            //         ]
            //     ]
            // ]
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
];
