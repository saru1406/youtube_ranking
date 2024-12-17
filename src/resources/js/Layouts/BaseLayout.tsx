import Sidebar from '@/Components/Sidebar';
import { PropsWithChildren } from 'react';

export default function BaseLayout({
    children,
    path,
}: PropsWithChildren & { path: string }) {
    return (
        <div className='flex flex-col h-screen'>
            <div className='flex flex-1'>
                <Sidebar path={path}></Sidebar>
                <div className='flex-1 flex flex-col bg-white'>
                    <div className='container mx-auto mb-4 flex-1 overflow-y-auto'>
                        {children}
                    </div>
                </div>
            </div>
        </div>
    );
}
