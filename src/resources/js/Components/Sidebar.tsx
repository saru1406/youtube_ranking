import { Link } from '@inertiajs/react';

export default function Sidebar({ path }: { path: string }) {
    return (
        <div className='max-w-64 min-w-64 bg-gradient-to-b from-red-700 via-pink-600 to-violet-600 text-white sticky top-0 h-screen'>
            {/* <div className='max-w-56 min-w-52 bg-gradient-to-b from-rose-800 via-rose-600 to-rose-100 text-white sticky top-0 h-screen'></div> */}
            <div className='flex flex-col h-full justify-end'>
                <div>
                    <Link
                        href={route('daily.trend')}
                        className='hover:text-gray-300 block p-3 text-left text-2xl mb-10'
                    >
                        RankingTube
                    </Link>
                    <Link
                        href={route('daily.trend')}
                        className={`block py-4 pl-8 my-3 ${
                            path === route('daily.trend')
                                ? 'text-white bg-gradient-to-r from-gray-700 via-gray-500 to-white'
                                : 'hover:text-white hover:bg-gradient-to-r from-gray-700 via-gray-500 to-white'
                        }`}
                    >
                        急上昇
                    </Link>
                    <Link
                        href={route('week.trend')}
                        className={`block py-4 pl-8 my-3 ${
                            path === route('week.trend')
                                ? 'text-white bg-gradient-to-r from-gray-700 via-gray-500 to-white'
                                : 'hover:text-white hover:bg-gradient-to-r from-gray-700 via-gray-500 to-white'
                        }`}
                    >
                        週間ランキング
                    </Link>
                    <Link
                        href={route('month.trend')}
                        className={`block p-4 pl-8 my-3 ${
                            path === route('month.trend')
                                ? 'text-white bg-gradient-to-r from-gray-700 via-gray-500 to-white'
                                : 'hover:text-white hover:bg-gradient-to-r from-gray-700 via-gray-500 to-white'
                        }`}
                    >
                        月間ランキング
                    </Link>
                    <Link
                        href='#'
                        className={`block p-4 pl-8 my-3 ${
                            path === '#'
                                ? 'text-white bg-gradient-to-r from-gray-700 via-gray-500 to-white'
                                : 'hover:text-white hover:bg-gradient-to-r from-gray-700 via-gray-500 to-white'
                        }`}
                    >
                        Coming Soon
                    </Link>
                </div>
                <div className='flex-1 flex flex-col justify-end mb-5'>
                    <Link
                        href='#'
                        className={`block py-5 pl-8 text-left text-black mb-10 ${
                            path === '#'
                                ? 'bg-gray-600 text-white'
                                : 'hover:bg-gray-600'
                        }`}
                        as='button'
                    >
                        お問い合わせ
                    </Link>
                    {/* <Link
                        href={route('logout')}
                        method='post'
                        className='hover:bg-gray-600 block py-5 pl-8 text-left text-black'
                        as='button'
                    >
                        ログアウト
                    </Link> */}
                </div>
            </div>
        </div>
    );
}
