import PaginationFooter from '@/Components/PaginationFooter';
import PaginationTop from '@/Components/PaginationTop';
import BaseLayout from '@/Layouts/BaseLayout';
import { DailyTrend } from '@/types/daily_trend';
import { Pagination } from '@/types/pagination';
import { Head, Link } from '@inertiajs/react';

export default function TrendIndex({
    trend_data,
}: {
    trend_data: Pagination<DailyTrend>;
}) {
    const fullPath = window.location.pathname;
    const path = fullPath.replace('/', '');

    const formatViewCount = (count: number) => {
        if (count >= 10000) {
            return `${Math.floor(count / 10000)}万`;
        }
        return count.toLocaleString();
    };
    const categoryLabels: { [key: string]: string } = {
        generals: '総合',
        'video-productions': 'テレビ・映像作品',
        musics: '音楽',
        sports: 'スポーツ',
        games: 'ゲーム',
        entertainments: 'エンターテインメント',
        news: 'ニュース',
        'how-to': '知識・アイディア',
    };

    return (
        <>
            <Head title='RankTube - YouTube再生回数・トレンドランキング' />
            <BaseLayout>
                <div className='mb-16 mt-6 mx-14'>
                    <div className='text-sm mb-10'>
                        <Link
                            href={route('daily.trend')}
                            className='text-blue-700'
                        >
                            急上昇ジャンル別ランキング
                        </Link>
                        ＞ 急上昇
                        {categoryLabels[path] || `カテゴリー ${path}`}ランキング
                    </div>
                    <div className='mb-10'>
                        <div className='flex items-center'>
                            <h2 className='text-2xl'>
                                急上昇
                                {categoryLabels[path] || `カテゴリー ${path}`}
                                ランキング
                            </h2>
                            <div className='ml-auto'>
                                <PaginationTop
                                    links={trend_data.links}
                                ></PaginationTop>
                            </div>
                        </div>

                        <div className='relative'>
                            <div className='absolute bottom-0 left-0 w-full h-0.5 bg-gradient-to-r from-red-500 via-yellow-500 to-yellow-100'></div>
                        </div>
                    </div>

                    <div className='mt-5 mb-10 w-11/12'>
                        {trend_data.data.map((trend) => (
                            <div className='' key={trend.video_id}>
                                <a
                                    className='flex my-3'
                                    href={trend.url}
                                    target='_blank'
                                    rel='noopener noreferrer'
                                >
                                    <div className='relative w-[246px] h-[138px] flex-shrink-0'>
                                        <img
                                            src={`https://img.youtube.com/vi/${trend.video_id}/hqdefault.jpg`}
                                            alt={trend.title}
                                            className='w-full h-full object-cover rounded-md'
                                        />
                                    </div>
                                    <div className='ml-4'>
                                        <p className='font-bold'>
                                            {trend.title}
                                        </p>
                                        <div className='flex text-xs my-2'>
                                            <p className='text-gray-500'>
                                                {trend.channel_name}
                                            </p>
                                            <p className='ml-4'>
                                                {formatViewCount(
                                                    trend.view_count
                                                )}
                                                <span className='ml-1'>
                                                    回視聴
                                                </span>
                                            </p>
                                            <p className='ml-4'>
                                                {trend.published_at
                                                    ? new Date(
                                                          trend.published_at
                                                      ).toLocaleDateString()
                                                    : '不明な日時'}
                                            </p>
                                            <p className='ml-4'>
                                                {trend.duration
                                                    ? trend.duration
                                                    : null}
                                            </p>
                                        </div>
                                        <p className='text-xs'>
                                            {trend.description?.length > 150
                                                ? `${trend.description.substring(0, 150)}...`
                                                : trend.description || null}
                                        </p>
                                    </div>
                                </a>
                                <hr />
                            </div>
                        ))}
                    </div>
                    <PaginationFooter
                        links={trend_data.links}
                    ></PaginationFooter>
                </div>
            </BaseLayout>
        </>
    );
}
