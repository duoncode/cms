export interface Field {
    rows: number | null;
    width: number | null;
    required: boolean;
    description: string | null;
    label: string;
    name: string;
    type: string;
}

export interface TextField extends Field {
    translate: boolean;
}
