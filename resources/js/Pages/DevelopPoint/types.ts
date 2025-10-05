import { Member } from "../Member/types";

interface TotalPoint {
    totalPoint: number;
    totalTarget: number;
}

export interface MemberTotalPoint extends TotalPoint {
    notionId: Member["notionId"];
}

export interface InReviewDateTotalPoint extends TotalPoint {
    inReviewDate: string;
}
export interface DevelopPointHistory {
    dateRange: DevelopPointDateRange;
    points: DevelopPoint[];
    memberTotalPoints: MemberTotalPoint[];
    inReviewDateTotalPoints: InReviewDateTotalPoint[];
    totalPoint: TotalPoint;
    updatedAt: string | null;
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
