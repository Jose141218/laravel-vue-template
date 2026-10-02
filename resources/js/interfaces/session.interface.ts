export interface SessionAgent {
    is_desktop: boolean;
    is_mobile: boolean;
    is_tablet: boolean;
    platform: string;
    browser: string;
}

export interface SessionDevice {
    id: string;
    agent: SessionAgent;
    ip_address: string;
    is_current_device: boolean;
    last_active: string;
}
