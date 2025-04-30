export interface Member {
    id: string;
    name: string;
    team: Team;
    imageUrl: string;
    targetPoint: number;
    kpis: KPI[];
    isValid: boolean;
}

export interface Team {
    id: number;
    name: string;
}

export interface KPI {
    id: number;
    content: string;
}
