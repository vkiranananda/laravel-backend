export default {
  // Форматирование размера файла
  formatFileSize(bytes) {
    if (!bytes || bytes === 0) return '';
    const k = 1024;
    const sizes = ['Б', 'КБ', 'МБ', 'ГБ'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i)) + ' ' + sizes[i];
  },
};