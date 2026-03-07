import React from 'react';
import IconProps from '../IconProps'

const Icon: React.FC<IconProps> = ({ className, alt, color }) => (
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        className={className+' SocialLogin:Glyph'}
        aria-labelledby={alt}
        color={color || "currentColor"}
        fill={"none"}
    >
        <path d="M8.75 3.75H15.25V8.25H8.75V3.75Z" fill="currentColor" />
        <path d="M8.75 15.25H15.25V10.25H20.25V20.25H3.75V10.25H8.75V15.25Z" fill="currentColor" />
    </svg>
);

export default Icon;