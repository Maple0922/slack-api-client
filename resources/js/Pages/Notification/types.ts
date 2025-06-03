export interface Notification {
    id: number;
    title: string;
    path: string;
    channel: {
        name: string;
        url: string;
    };
}
