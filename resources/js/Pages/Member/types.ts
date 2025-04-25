export interface Member {
    id: string;
    name: string;
    team: Team;
    imageUrl: string;
    targetPoint: number;
    isValid: boolean;
}

export interface Team {
    id: number;
    name: string;
}
