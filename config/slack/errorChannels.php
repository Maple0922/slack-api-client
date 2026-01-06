<?php

return [
    [
        'key' => 'errorCrmAdminProduction',
        'name' => '50_error_crm_admin_production',
        'channelId' => env('SLACK_CHANNEL_ID_ERROR_CRM_ADMIN_PRODUCTION')
    ],
    [
        'key' => 'errorCrmAdminKernelProduction',
        'name' => 'error_crm_admin_kernel_production',
        'channelId' => env('SLACK_CHANNEL_ID_ERROR_CRM_ADMIN_KERNEL_PRODUCTION')
    ],
    [
        'key' => 'errorCrmExpertCalendarProduction',
        'name' => 'error_crm_expert_calendar_production',
        'channelId' => env('SLACK_CHANNEL_ID_ERROR_CRM_EXPERT_CALENDAR_PRODUCTION')
    ],
    [
        'key' => 'errorCrmMarketCloudKernelProduction',
        'name' => 'error_crm_market_cloud_kernel_production',
        'channelId' => env('SLACK_CHANNEL_ID_ERROR_CRM_MARKET_CLOUD_KERNEL_PRODUCTION')
    ],
    [
        'key' => 'errorCrmExpertKernelProduction',
        'name' => 'error_crm_expert_kernel_production',
        'channelId' => env('SLACK_CHANNEL_ID_ERROR_CRM_EXPERT_KERNEL_PRODUCTION')
    ],
];
