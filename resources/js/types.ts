import type { Item } from "vue3-easy-data-table";

export interface ErrorCount extends Item {
    week: string;
    crmAdmin: number;
    crmExpert: number;
    crmMarketHolder: number;
    crmBot: number;
    moneyCareer: number;
}

export interface ErrorList extends Item {
    datetime: string;
    content: string;
}

export interface SDCount extends Item {
    week: string;
    count: number;
}

export interface SDList extends Item {
    datetime: string;
    content: string;
}
