import React from 'react';
import IconMapping from './IconMapping';

interface ProviderIconProps {
    name: string;
    short_name: string;
    color?: string;
    className?: string;
}

const ProviderIcon: React.FC<ProviderIconProps> = ({ name, short_name, color, className }) => {
    const IconComponent = IconMapping[short_name] || IconMapping['generic']; 
    
    return (
        <>
            <IconComponent
                className={className}
                alt={`${name} icon`}
                color={color || "#000000"}
            />
        </>
    )
}

export default ProviderIcon;