import Sidebar from '@/Components/Sidebar';
import BaseLayout from '@/Layouts/BaseLayout';
import { PageProps } from '@/types';
import { DailyTrend } from '@/types/daily_trend';
import { Head, Link } from '@inertiajs/react';
import { useState } from 'react';
import { format } from 'date-fns';

export default function TrendIndex({
    trend_data,
}: {
    trend_data: DailyTrend[][];
}) {
    const formatViewCount = (count: number) => {
        if (count >= 10000) {
            return `${Math.floor(count / 10000)}万`;
        }
        return count.toLocaleString();
    };
    const categoryLabels: { [key: string]: string } = {
        '0': '総合',
        '1': 'テレビ・映像作品',
        '10': '音楽',
        '17': 'スポーツ',
        '20': 'ゲーム',
        '24': 'エンターテインメント',
        '25': 'ニュース',
        '26': '知識・アイディア',
    };
    const [isVideoPlaying, setIsVideoPlaying] = useState(false);
    return (
        <>
            <Head title='RankTube - YouTube再生回数・トレンドランキング' />
            <BaseLayout>
                <div className='mt-16 mx-14'>
                    <div className='relative mb-10'>
                        <h2 className='text-2xl'>急上昇ジャンル別ランキング</h2>
                        <div className='absolute bottom-0 left-0 w-full h-0.5 bg-gradient-to-r from-red-500 via-yellow-500 to-yellow-100'></div>
                    </div>
                    <div className='my-5 w-11/12'>
                        {Object.entries(trend_data).map(([key, trends]) => (
                            <div key={key} className='mb-16'>
                                <div className='relative mb-10'>
                                    <div className='flex'>
                                        <Link href="" className='text-xl'>
                                            {categoryLabels[key] ||
                                                `カテゴリー ${key}`}
                                        </Link>
                                    </div>
                                    <div className='absolute bottom-0 left-0 w-full h-0.5 bg-gradient-to-r from-blue-500 via-emerald-400 to-green-100'></div>
                                </div>
                                {trends.map((trend: any) => (
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
                            </div>
                        ))}
                    </div>
                </div>
            </BaseLayout>
        </>
    );
}
