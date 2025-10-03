interface WorkingDayMember {
    id: string;
    name: string;
}

export interface WorkingDay {
    date: string;
    week: string;
    isSaturday: boolean;
    isSunday: boolean;
    members: WorkingDayMember[];
}
