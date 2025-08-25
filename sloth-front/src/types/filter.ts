export type FiltersList = {
    price: {
        min: number;
        max: number
    };
};

export type State = {
    filtersList: FiltersList;
    loaded: boolean;
    error: string | null;
};