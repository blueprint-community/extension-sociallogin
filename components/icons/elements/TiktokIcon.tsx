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
        <path d="M9.5 22C12.8137 22 15.5 19.3137 15.5 16V8.24537C16.7481 9.24588 18.3025 9.88001 20.0003 9.98464C20.2759 10.0016 20.5 9.77614 20.5 9.5V7.5C20.5 7.22386 20.2755 7.00263 20.0007 6.97538C17.6379 6.74113 15.7589 4.86207 15.5246 2.4993C15.4974 2.2245 15.2761 2 15 2H13C12.7239 2 12.5 2.22386 12.5 2.5V16C12.5 17.6569 11.1569 19 9.5 19C7.84315 19 6.5 17.6569 6.5 16C6.5 14.5128 7.58221 13.2783 9.00209 13.0411C9.27446 12.9956 9.5 12.7761 9.5 12.5V10.5C9.5 10.2239 9.27536 9.99783 9.00015 10.0205C5.92031 10.2745 3.5 12.8546 3.5 16C3.5 19.3137 6.18629 22 9.5 22Z" fill="currentColor" />
    </svg>
);

export default Icon;