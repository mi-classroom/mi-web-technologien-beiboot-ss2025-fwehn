interface Folder extends Iptc {
    id?: number;
    name?: string;
    parent_folder_id?: number;
    parent_folder?: Folder;
}
