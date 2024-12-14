import type {User} from "@/Types/models";

export interface DashboardPageProps {
    users: PaginationMeta<User[]>;
    query: Record<string, any>
}

type NullOrString = string | null

export interface PaginationMeta<T> {
    data: T,
    first_page_url: NullOrString;
    prev_page_url: NullOrString,
    next_page_url: NullOrString,
    last_page_url: NullOrString,
    current_page: number;
    last_page: number,
    links: PaginationLink[],
    path: string,
    per_page: number,
    from: number,
    to: number,
    total: number
}

export interface PaginationLink {
    url: NullOrString,
    label: string,
    active: boolean
}
