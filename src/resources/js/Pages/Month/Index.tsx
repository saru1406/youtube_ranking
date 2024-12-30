import BaseLayout from '@/Layouts/BaseLayout';
import { DailyTrend } from '@/types/daily_trend';
import { Head, Link } from '@inertiajs/react';

export default function WeekIndex({
    trend_data,
}: {
    trend_data: DailyTrend[][];
}) {
    const formatViewCount = (count: number): string => {
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
            <BaseLayout path={route('month.trend')}>
                <div className='mt-12 mx-14'>
                    <div className='relative mb-10'>
                        <h2 className='text-2xl'>月間ジャンル別ランキング</h2>
                        <div className='absolute bottom-0 left-0 w-full h-0.5 bg-gradient-to-r from-red-400 via-yellow-500 to-yellow-100'></div>
                    </div>
                    <div className='my-5 w-11/12 ml-7'>
                        {Object.entries(trend_data).map(([key, trends]) => (
                            <div key={key} className='mb-10'>
                                <div className='relative mb-5'>
                                    <div className='flex'>
                                        <Link
                                            href={route(
                                                'month.trend.category',
                                                key
                                            )}
                                            className='text-xl'
                                        >
                                            {categoryLabels[key] ||
                                                `カテゴリー ${key}`}
                                        </Link>
                                    </div>
                                    <div className='absolute bottom-0 left-0 w-full h-0.5 bg-gradient-to-r from-blue-400 via-emerald-400 to-green-100'></div>
                                </div>
                                {trends.map((trend: any) => (
                                    <div className='' key={trend.video_id}>
                                        <a
                                            className='flex my-2 p-2 hover:shadow-xl bg-white hover:bg-gray-50 transition-colors duration-300 rounded-md'
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
                                                    {trend.title?.length > 85
                                                        ? `${trend.title.substring(0, 85)}...`
                                                        : trend.title || null}
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
                                                    {trend.description?.length >
                                                    150
                                                        ? `${trend.description.substring(0, 150)}...`
                                                        : trend.description ||
                                                          null}
                                                </p>
                                            </div>
                                        </a>
                                        <hr />
                                    </div>
                                ))}
                                <div className='text-right mt-5'>
                                    <Link
                                        href={route(
                                            'month.trend.category',
                                            key
                                        )}
                                        className='text-blue-700'
                                    >
                                        もっとみる
                                    </Link>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </BaseLayout>
        </>
    );
}
