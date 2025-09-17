export interface Notification {
    title: string;
    key: string;
    channel: string;
}

export interface Channel {
    key: string;
    name: string;
    link: string;
    webhookUrl: string;
}
