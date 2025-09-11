import { User } from '@/types/index';

interface Image {
    readonly id?: number;
    name?: string;
    width;
    height;
    original_path;
    preview_url?: string;
    iptc?: Iptc;

    user_id;
    user: User;

    folder_id;
    folder: Folder;

    fill_percent?: number;
}
