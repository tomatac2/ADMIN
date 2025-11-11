import React from 'react';
import { Head } from '@inertiajs/react';

interface AppLayoutProps {
    children: React.ReactNode;
    title?: string;
}

export default function AppLayout({ children, title }: AppLayoutProps) {
    return (
        <>
            <Head title={title} />
            <div className="min-h-screen bg-gray-50">
                {children}
            </div>
        </>
    );
}
