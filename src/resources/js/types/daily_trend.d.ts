import { Category } from '@/types/category';
export type DailyTrend = {
    id: number;
    video_id: string;
    title: string;
    description: string;
    channel_id: string;
    channel_name: string;
    view_count: number;
    like_count: number;
    comment_count: number;
    duration: string;
    category_id: number;
    url: string;
    published_at: string;
    category: Category;
};
