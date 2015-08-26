QuantimodoMath = function () {
    /* Pearson correlation coefficient calculator */
    var correlationCoefficient = function (points) {
        var length = points.length, sumX = 0, sumY = 0, sumXX = 0, sumXY = 0, sumYY = 0;

        for (var i = 0; i < length; i++) {
            var point = points[i], x = point.x, y = point.y;
            sumX += x;
            sumY += y;
            sumXX += x * x;
            sumXY += x * y;
            sumYY += y * y;
        }

        return (length * sumXY - sumX * sumY) / Math.sqrt((length * sumXX - sumX * sumX) * (length * sumYY - sumY * sumY));
    };

    /* Weighted Theil-Sen estimator segment generator
     Usage example:
     var result = linearRegression([{ x: 5, y: 2 }, { x: 6, y: 10 }, { x: 7, y: 85 }]);
     var left = result[0];
     var right = result[1];
     alert("left: " + left + ", right: " + right);
     */
    var linearRegressionEndpoints = function (points, min, max) {
        if (points.length <= 1) {
            return [];
        }

        var i, xDiffSqSum = 0, slopes = [], minX = min ? min : Infinity, maxX = max ? max : -Infinity;
        for (i = 0; i < points.length; i++) {
            var point1 = points[i], x1 = point1.x;
            minX = Math.min(minX, x1);
            maxX = Math.max(maxX, x1);
            for (var j = i + 1; j < points.length; j++) {
                var point2 = points[j], x2 = point2.x;
                if (x1 != x2) {
                    var xDiff = x1 - x2, xDiffSq = xDiff * xDiff;
                    xDiffSqSum += xDiffSq;
                    slopes.push([xDiffSq, (point1.y - point2.y) / xDiff]);
                }
            }
        }
        xDiffSqSum *= 0.5;
        slopes.sort(function (a, b) {
            a[1] < b[1] ? -1 : 1
        });
        var m;
        for (i = 0; i < slopes.length; i++) {
            var slope = slopes[i];
            xDiffSqSum -= slope[0];
            if (xDiffSqSum <= 0) {
                m = slope[1];
                break;
            }
        }

        var yIntercepts = [];
        for (i = 0; i < points.length; i++) {
            var point = points[i];
            yIntercepts.push(point.y - m * point.x);
        }
        yIntercepts.sort(function (a, b) {
            a < b ? -1 : 1
        });
        medianIndex = 0.5 * yIntercepts.length;
        flooredMedianIndex = Math.floor(medianIndex);
        var b = flooredMedianIndex === medianIndex ? yIntercepts[flooredMedianIndex] : 0.5 * (yIntercepts[flooredMedianIndex] + yIntercepts[flooredMedianIndex + 1]);

        return [[minX, m * minX + b], [maxX, m * maxX + b]];
    };

    /* Fritsch-Carlson monotone cubic spline interpolation
     Usage example:
     var f = createInterpolant([0, 1, 2, 3], [0, 1, 4, 9]);
     var message = '';
     for (var x = 0; x <= 3; x += 0.5) {
     var xSquared = f(x);
     message += x + ' squared is about ' + xSquared + '\n';
     }
     alert(message);
     */
    var createInterpolant = function (xs, ys) {
        var i, length = xs.length;

        // Deal with length issues
        if (length != ys.length) {
            throw 'Need an equal count of xs and ys.';
        }
        if (length === 0) {
            return function (x) {
                return 0;
            };
        }
        if (length === 1) {
            // Impl: Precomputing the result prevents problems if ys is mutated later and allows garbage collection of ys
            // Impl: Unary plus properly converts values to numbers
            var result = +ys[0];
            return function (x) {
                return result;
            };
        }

        // Rearrange xs and ys so that xs is sorted
        var indexes = [];
        for (i = 0; i < length; i++) {
            indexes.push(i);
        }
        indexes.sort(function (a, b) {
            return xs[a] < xs[b] ? -1 : 1;
        });
        var oldXs = xs, oldYs = ys;
        // Impl: Creating new arrays also prevents problems if the input arrays are mutated later
        xs = [];
        ys = [];
        // Impl: Unary plus properly converts values to numbers
        for (i = 0; i < length; i++) {
            xs.push(+oldXs[indexes[i]]);
            ys.push(+oldYs[indexes[i]]);
        }

        // Get consecutive differences and slopes
        var dys = [], dxs = [], ms = [];
        for (i = 0; i < length - 1; i++) {
            var dx = xs[i + 1] - xs[i], dy = ys[i + 1] - ys[i];
            dxs.push(dx);
            dys.push(dy);
            ms.push(dy / dx);
        }

        // Get degree-1 coefficients
        var c1s = [ms[0]];
        for (i = 0; i < dxs.length - 1; i++) {
            var m = ms[i], mNext = ms[i + 1];
            if (m * mNext <= 0) {
                c1s.push(0);
            } else {
                var dx = dxs[i], dxNext = dxs[i + 1], common = dx + dxNext;
                c1s.push(3 * common / ((common + dxNext) / m + (common + dx) / mNext));
            }
        }
        c1s.push(ms[ms.length - 1]);

        // Get degree-2 and degree-3 coefficients
        var c2s = [], c3s = [];
        for (i = 0; i < c1s.length - 1; i++) {
            var c1 = c1s[i], m = ms[i], invDx = 1 / dxs[i], common = c1 + c1s[i + 1] - m - m;
            c2s.push((m - c1 - common) * invDx);
            c3s.push(common * invDx * invDx);
        }

        // Return interpolant function
        return function (x) {
            // The rightmost point in the dataset should give an exact result
            var i = xs.length - 1;
            if (x == xs[i]) {
                return ys[i];
            }

            // Search for the interval x is in, returning the corresponding y if x is one of the original xs
            var low = 0, mid, high = c3s.length - 1;
            while (low <= high) {
                mid = Math.floor(0.5 * (low + high));
                var xHere = xs[mid];
                if (xHere < x) {
                    low = mid + 1;
                }
                else if (xHere > x) {
                    high = mid - 1;
                }
                else {
                    return ys[mid];
                }
            }
            i = Math.max(0, high);

            // Interpolate
            var diff = x - xs[i], diffSq = diff * diff;
            return ys[i] + c1s[i] * diff + c2s[i] * diffSq + c3s[i] * diff * diffSq;
        };
    };

    return {
        correlationCoefficient: correlationCoefficient,
        linearRegressionEndpoints: linearRegressionEndpoints,
        createInterpolant: createInterpolant
    };
}();
