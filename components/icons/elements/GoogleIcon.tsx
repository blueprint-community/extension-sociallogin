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
        <path fillRule="evenodd" clipRule="evenodd" d="M12 1.25C6.06294 1.25 1.25 6.06294 1.25 12C1.25 17.9371 6.06294 22.75 12 22.75C17.9371 22.75 22.75 17.9371 22.75 12C22.75 6.06294 17.9371 1.25 12 1.25ZM8 12C8 9.79086 9.79086 8 12 8C13.1048 8 14.1035 8.44662 14.8284 9.17157L16.2426 7.75736C15.1579 6.67267 13.6566 6 12 6C8.68629 6 6 8.68629 6 12C6 15.3137 8.68629 18 12 18C15.3137 18 18 15.3137 18 12V11H12V13H15.874C15.4299 14.7252 13.8638 16 12 16C9.79086 16 8 14.2091 8 12Z" fill="currentColor" />
    </svg>
);

export default Icon;