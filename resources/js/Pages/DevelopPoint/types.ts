import { Member } from "../Member/types";

export interface DevelopPointHistory {
    dateRange: DevelopPointDateRange;
    points: DevelopPoint[];
}

export interface DevelopPointDateRange {
    start: string;
    end: string;
}

export interface DevelopPoint {
    inReviewDate: string;
    members: DevelopPointMember[];
}

export interface DevelopPointMember extends Omit<Member, "kpis" | "isValid"> {
    point: number;
    target: number;
}
