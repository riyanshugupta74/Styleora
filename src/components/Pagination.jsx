import { Link } from 'react-router-dom';

const Pagination = ({ meta, onPageChange }) => {
    if (!meta || meta.last_page <= 1) {
        return null;
    }

    const { current_page, last_page, links } = meta;

    const handlePageClick = (e, url) => {
        if (!url) {
            e.preventDefault();
            return;
        }
        if (onPageChange) {
            e.preventDefault();
            const urlObj = new URL(url);
            const page = urlObj.searchParams.get('page');
            onPageChange(page);
        }
    };

    return (
        <div className="flex justify-center mt-12 mb-8">
            <nav className="inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                {links.map((link, index) => {
                    let label = link.label;
                    const labelStr = String(label);
                    if (labelStr.includes('Previous')) label = <i className="fa-solid fa-chevron-left text-xs"></i>;
                    if (labelStr.includes('Next')) label = <i className="fa-solid fa-chevron-right text-xs"></i>;

                    const isCurrent = link.active;
                    const isDisabled = !link.url;

                    return (
                        <a
                            key={index}
                            href={link.url || '#'}
                            onClick={(e) => handlePageClick(e, link.url)}
                            className={`
                                relative inline-flex items-center px-4 py-2 border text-sm font-medium
                                ${index === 0 ? 'rounded-l-md' : ''}
                                ${index === links.length - 1 ? 'rounded-r-md' : ''}
                                ${isCurrent ? 'z-10 bg-black border-black text-white' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'}
                                ${isDisabled ? 'opacity-50 cursor-not-allowed' : ''}
                            `}
                        >
                            {typeof label === 'string' ? (
                                <span dangerouslySetInnerHTML={{ __html: label }} />
                            ) : (
                                label
                            )}
                        </a>
                    );
                })}
            </nav>
        </div>
    );
};

export default Pagination;
