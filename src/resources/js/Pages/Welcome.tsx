import BaseLayout from '@/Layouts/BaseLayout';
import { DailyTrend } from '@/types/daily_trend';
import { Head, Link } from '@inertiajs/react';

export default function Welcome({
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
            <BaseLayout path={route('daily.trend')}>
                <div className='mt-12 mx-14'>
                    <div className='relative mb-10'>
                        <h2 className='text-2xl'>急上昇ジャンル別ランキング</h2>
                        <div className='absolute bottom-0 left-0 w-full h-0.5 bg-gradient-to-r from-rose-400 via-fuchsia-500 to-indigo-500 bg-[length:100%_6px] bg-no-repeat'></div>
                    </div>
                    <div className='flex'>
                        <div className='my-5 w-9/12 ml-7'>
                            {Object.entries(trend_data).map(([key, trends]) => (
                                <div key={key} className='mb-10'>
                                    <div className='relative mb-5'>
                                        <div className='flex'>
                                            <Link
                                                href={route(
                                                    'daily.trend.category',
                                                    key
                                                )}
                                                className='text-xl'
                                            >
                                                {categoryLabels[key] ||
                                                    `カテゴリー ${key}`}
                                            </Link>
                                        </div>
                                        <div className='absolute bottom-0 left-0 w-full h-0.5 bg-gradient-to-r from-orange-500 via-amber-400 to-yellow-300'></div>
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
                                                    <h2 className='relative text-4xl font-bold text-white'>
                                                        <span className='absolute -left-4 top-1/2 -translate-y-1/2 px-4 py-2 text-2xl font-extrabold italic text-white rounded-full bg-gradient-to-br from-blue-500 to-purple-500 shadow-lg tracking-wide drop-shadow-lg'>
                                                            {trend.ranking}位
                                                        </span>
                                                    </h2>
                                                    <img
                                                        src={`https://img.youtube.com/vi/${trend.video_id}/hqdefault.jpg`}
                                                        alt={trend.title}
                                                        className='w-full h-full object-cover rounded-md'
                                                    />
                                                </div>
                                                <div className='ml-4'>
                                                    <p className='font-bold'>
                                                        {trend.title?.length >
                                                        85
                                                            ? `${trend.title.substring(0, 85)}...`
                                                            : trend.title ||
                                                              null}
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
                                                        {trend.description
                                                            ?.length > 130
                                                            ? `${trend.description.substring(0, 130)}...`
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
                                                'daily.trend.category',
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
                        <div className='my-5 w-3/12 ml-7'>
                            <div className='relative mb-5'>
                                <div className='flex'>
                                    <p className='text-xl'>アーカイブ</p>
                                </div>
                                <div className='absolute bottom-0 left-0 w-full h-0.5 bg-gradient-to-r from-blue-500 via-green-500 to-emerald-300'></div>
                            </div>
                        </div>
                    </div>
                </div>
            </BaseLayout>
        </>
    );
}
