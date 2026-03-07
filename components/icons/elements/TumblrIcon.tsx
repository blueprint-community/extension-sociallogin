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
        <path d="M9 11.0491H6V7.58386C8.5 6.51539 10 5.01026 10.5 2H13.7625V7.58386H17.644V11.0491H13.7629V16.3085C13.7629 17.8956 15.2687 18.1276 16.5693 17.7162C16.784 17.6482 16.8913 17.6143 16.9658 17.6543C17.0403 17.6944 17.0689 17.795 17.1262 17.9961L18 21.065C14.0681 23.3323 9.00037 21.4357 9.00037 15.8151L9 11.0491Z" fill="currentColor" />
    </svg>
);

export default Icon;