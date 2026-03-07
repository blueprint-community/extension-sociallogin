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
        <path d="M12 2.25C12.2724 2.25 12.5233 2.39767 12.6556 2.63577L22.6556 20.6358C22.7847 20.8681 22.7812 21.1513 22.6464 21.3803C22.5116 21.6094 22.2657 21.75 22 21.75H2C1.73426 21.75 1.48836 21.6094 1.3536 21.3803C1.21883 21.1513 1.21533 20.8681 1.34438 20.6358L11.3444 2.63577C11.4767 2.39767 11.7276 2.25 12 2.25Z" fill="currentColor" />
    </svg>
);

export default Icon;