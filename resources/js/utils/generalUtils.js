import { v4 as uuidv4 } from 'uuid';

export function generateUniqueId(prefix = null) {
    return (prefix == null ? '' : prefix + '_') + uuidv4().replace(/-/g, '_');
}
