INSERT INTO
    users (
        "name",
        "email",
        "password"
    )
VALUES
    (
        'Futo Nakajima',
        'futo.nakajima@wizleap.co.jp',
        '$2y$10$l4jN.UbXs.C2Ff64TNq9sOQTnZGHgFIAt6gK36CqIu4JwTS5dqnHK'
    );


INSERT INTO
    teams ("id", "name")
VALUES
    (1, 'Backend'),
    (2, 'Frontend'),
    (3, 'Security');


INSERT INTO
    members (
        "notion_id",
        "name",
        "team_id",
        "image_url",
        "target_point",
        "is_valid"
    )
VALUES
    (
        'c397fd54-4c8e-42e4-8772-6ed72b895462',
        'Hiroki Nagai',
        1,
        'https://i.imgur.com/3Rx3uBg.png',
        40,
        1
    ),
    (
        'ea7d4fe6-ff62-4673-ae68-c6be6606cc9c',
        'Issei Hayashi',
        2,
        'https://i.imgur.com/oQFsqwz.png',
        20,
        1
    ),
    (
        'e1f0d9f3-e08e-4b75-a617-05fbbd385b42',
        'Futo Nakajima',
        1,
        'https://i.imgur.com/YM3dAve.jpeg',
        20,
        1
    ),
    (
        '1dc456a2-9f6c-4d03-b498-a84526440ba1',
        'Takuma Kawazu',
        1,
        'https://i.imgur.com/2h0VYss.jpeg',
        30,
        1
    ),
    (
        'f34d832f-0bd2-4fbc-9b28-143b0a6a4e53',
        'Kengo Kitaku',
        NULL,
        'https://i.imgur.com/fJbKnfI.png',
        0,
        '0'
    ),
    (
        '112d872b-594c-81ca-b227-000203f0a8cb',
        'Kazuya Matsumoto',
        1,
        'https://i.imgur.com/OiYwaIN.png',
        30,
        1
    ),
    (
        'a15a19c0-3034-4f2d-98f3-6ff195f7a4da',
        'Kentaro Tada',
        2,
        'https://i.imgur.com/bA1ibze.png',
        30,
        1
    ),
    (
        '5301f1c9-08d0-4549-830f-fc732d830ab5',
        'Tatsunori Ishikawa',
        3,
        'https://i.imgur.com/JsGSB3v.jpeg',
        0,
        '0'
    ),
    (
        '0d8b1c19-4d5c-4b2d-b38e-60a3e87d7439',
        'Shota Deyama',
        2,
        'https://i.imgur.com/g6e8uQv.jpeg',
        35,
        1
    ),
    (
        '173d872b-594c-8156-a5dd-00023a79c0c5',
        'Ryo Numoto',
        1,
        'https://i.imgur.com/C7U6Ohb.png',
        30,
        1
    ),
    (
        '1b2d872b-594c-8126-9aea-000261d33213',
        'Sawato Kawabe',
        2,
        'https://i.imgur.com/ReAc4VJ.png',
        30,
        1
    );


INSERT INTO
    kpis (
        member_notion_id,
        content,
        created_at,
        updated_at
    )
VALUES
    (
        'c397fd54-4c8e-42e4-8772-6ed72b895462',
        '開発pt 40pt',
        '2025-05-01 03:08:35',
        '2025-05-01 03:08:35'
    ),
    (
        'c397fd54-4c8e-42e4-8772-6ed72b895462',
        '大型アップデート（CS自動化、AIレポート、Webチャット/LINE自動化）',
        '2025-05-01 03:08:35',
        '2025-05-01 03:08:49'
    ),
    (
        'e1f0d9f3-e08e-4b75-a617-05fbbd385b42',
        'チーム全体の開発pt 目標達成率100%',
        '2025-05-01 03:10:51',
        '2025-05-01 03:10:51'
    ),
    (
        'e1f0d9f3-e08e-4b75-a617-05fbbd385b42',
        'QAチームの独立（QAリストアップ、評価基準の導入、MCMC全体のQA項目のリストアップ）',
        '2025-05-01 03:10:51',
        '2025-05-01 03:10:51'
    ),
    (
        '5301f1c9-08d0-4549-830f-fc732d830ab5',
        'BCP対応 + WAF導入･運用',
        '2025-05-01 03:26:54',
        '2025-05-01 03:26:54'
    ),
    (
        '5301f1c9-08d0-4549-830f-fc732d830ab5',
        'MCMCの自社診断完了',
        '2025-05-01 03:26:54',
        '2025-05-01 03:26:54'
    ),
    (
        '0d8b1c19-4d5c-4b2d-b38e-60a3e87d7439',
        '開発pt 35pt',
        '2025-05-01 03:27:20',
        '2025-05-01 03:27:20'
    ),
    (
        'ea7d4fe6-ff62-4673-ae68-c6be6606cc9c',
        '直近1ヶ月のオープンからレビューまでの平均時間',
        '2025-05-01 03:28:09',
        '2025-05-01 03:28:09'
    ),
    (
        'ea7d4fe6-ff62-4673-ae68-c6be6606cc9c',
        'リアーキテクチャロードマップの作成率 100%',
        '2025-05-01 03:28:09',
        '2025-05-01 03:28:09'
    ),
    (
        '1dc456a2-9f6c-4d03-b498-a84526440ba1',
        '開発pt 30pt',
        '2025-05-01 03:29:05',
        '2025-05-01 03:29:05'
    ),
    (
        '1dc456a2-9f6c-4d03-b498-a84526440ba1',
        '大規模リリース（配信後フロー自動化、マッチングロジック導入、社内FAアカウント体制変更、社内FA配信カスタマイズ機能、マーケタスク依頼仕組み化）',
        '2025-05-01 03:29:05',
        '2025-05-01 03:29:05'
    ),
    (
        'a15a19c0-3034-4f2d-98f3-6ff195f7a4da',
        '開発pt 30pt',
        '2025-05-01 03:29:29',
        '2025-05-01 03:29:29'
    ),
    (
        'a15a19c0-3034-4f2d-98f3-6ff195f7a4da',
        'MCEC本リリース',
        '2025-05-01 03:29:29',
        '2025-05-01 03:29:29'
    ),
    (
        '112d872b-594c-81ca-b227-000203f0a8cb',
        '開発pt 30pt',
        '2025-05-01 03:29:48',
        '2025-05-01 03:29:48'
    ),
    (
        '173d872b-594c-8156-a5dd-00023a79c0c5',
        '開発pt 30pt',
        '2025-05-01 03:30:17',
        '2025-05-01 03:30:17'
    ),
    (
        '173d872b-594c-8156-a5dd-00023a79c0c5',
        '9月中にMCEC本リリース（Zoom連携、面談レポートカスタマイズ、Gateway連携）',
        '2025-05-01 03:30:17',
        '2025-05-01 03:30:17'
    ),
    (
        '1b2d872b-594c-8126-9aea-000261d33213',
        '開発pt 30pt',
        '2025-05-01 03:30:38',
        '2025-05-01 03:30:38'
    ),
    (
        '1b2d872b-594c-8126-9aea-000261d33213',
        'MCMA 社内リリース',
        '2025-05-01 03:30:38',
        '2025-05-01 03:30:38'
    );


INSERT INTO
    off_dates (
        `date`,
        `member_notion_id`
    )
SELECT
    '2025-04-29',
    notion_id
FROM
    members
WHERE
    deleted_at is null;


INSERT INTO
    off_dates (
        `date`,
        `member_notion_id`
    )
SELECT
    '2025-05-03',
    notion_id
FROM
    members
WHERE
    deleted_at is null;


INSERT INTO
    off_dates (
        `date`,
        `member_notion_id`
    )
SELECT
    '2025-05-04',
    notion_id
FROM
    members
WHERE
    deleted_at is null;


INSERT INTO
    off_dates (
        `date`,
        `member_notion_id`
    )
SELECT
    '2025-05-05',
    notion_id
FROM
    members
WHERE
    deleted_at is null;


INSERT INTO
    off_dates (
        `date`,
        `member_notion_id`
    )
SELECT
    '2025-05-06',
    notion_id
FROM
    members
WHERE
    deleted_at is null;


INSERT INTO
    off_dates (
        `date`,
        `member_notion_id`
    )
SELECT
    '2025-07-21',
    notion_id
FROM
    members
WHERE
    deleted_at is null;


INSERT INTO
    off_dates (
        `date`,
        `member_notion_id`
    )
SELECT
    '2025-08-11',
    notion_id
FROM
    members
WHERE
    deleted_at is null;


INSERT INTO
    off_dates (
        `date`,
        `member_notion_id`
    )
SELECT
    '2025-09-15',
    notion_id
FROM
    members
WHERE
    deleted_at is null;


INSERT INTO
    off_dates (
        `date`,
        `member_notion_id`
    )
SELECT
    '2025-09-23',
    notion_id
FROM
    members
WHERE
    deleted_at is null;


INSERT INTO
    off_dates (
        `date`,
        `member_notion_id`
    )
SELECT
    '2025-10-13',
    notion_id
FROM
    members
WHERE
    deleted_at is null;


INSERT INTO
    off_dates (
        `date`,
        `member_notion_id`
    )
VALUES
    (
        '2025-04-28',
        'a15a19c0-3034-4f2d-98f3-6ff195f7a4da'
    );
