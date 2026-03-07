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
        <path fillRule="evenodd" clipRule="evenodd" d="M11.977 22.75C17.9268 22.75 22.75 17.9371 22.75 12C22.75 6.06294 17.9268 1.25 11.977 1.25C6.36515 1.25 1.75549 5.53178 1.25 11H8.21898V9C8.21898 8.58579 8.55549 8.25 8.97058 8.25H14.9834C15.3985 8.25 15.735 8.58579 15.735 9V15C15.735 15.4142 15.3985 15.75 14.9834 15.75H8.97058C8.55549 15.75 8.21898 15.4142 8.21898 15V13H1.25C1.75549 18.4682 6.36515 22.75 11.977 22.75Z" fill="currentColor" />
    </svg>
);

export default Icon;