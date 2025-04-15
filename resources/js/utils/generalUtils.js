import { v4 as uuidv4 } from 'uuid';
import countries from '@Json/countries.json';

export function getCountryName(isoCode) {
    if (!isoCode) return '';
    const match = countries.find(c => c.iso.toLowerCase() === isoCode.toLowerCase());
    return match ? match.name : isoCode;
}

export function generateUniqueId(prefix = null) {
    return (prefix == null ? '' : prefix + '_') + uuidv4().replace(/-/g, '_');
}
