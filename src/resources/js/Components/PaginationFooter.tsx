import { Links } from '@/types/pagination';
import { Link } from '@inertiajs/react';

export default function PaginationFooter({ links }: { links: Links[] }) {
    return (
        <div className='flex justify-center'>
            {links?.map((link, index) => (
                <Link
                    key={index}
                    href={link.url || '#'}
                    className={`px-3 py-2 mx-1 border rounded-md text-sm ${
                        link.active
                            ? 'bg-blue-500 text-white border-blue-500'
                            : link.url
                              ? 'border-gray-300 text-gray-700 hover:bg-gray-100'
                              : 'border-gray-300 text-gray-400 cursor-not-allowed'
                    }`}
                    dangerouslySetInnerHTML={{
                        __html:
                            link.label === 'pagination.next'
                                ? '次へ ＞'
                                : link.label === 'pagination.previous'
                                  ? '＜ 戻る'
                                  : link.label
                                    ? link.label
                                    : '',
                    }}
                ></Link>
            ))}
        </div>
    );
}
